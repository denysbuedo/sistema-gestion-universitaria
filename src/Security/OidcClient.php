<?php

namespace App\Security;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OidcClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private bool $oidcEnabled,
        private string $oidcIssuerUrl,
        private string $oidcClientId,
        private string $oidcClientSecret,
        private string $oidcRedirectUri,
        private string $oidcPostLogoutRedirectUri,
        private string $oidcExpectedAudience,
        private string $oidcPkceMethod
    ) {
    }

    public function isEnabled(): bool { return $this->oidcEnabled; }

    public function begin(string $mode, ?int $userId = null): array
    {
        if (!$this->oidcEnabled || $this->oidcPkceMethod !== 'S256') {
            throw new \RuntimeException('La autenticación institucional no está disponible.');
        }
        $state = $this->randomValue();
        $nonce = $this->randomValue();
        $verifier = $this->randomValue(64);
        $metadata = $this->metadata();
        $query = http_build_query([
            'response_type' => 'code', 'client_id' => $this->oidcClientId,
            'redirect_uri' => $this->oidcRedirectUri,
            'scope' => 'openid profile email acr', 'state' => $state, 'nonce' => $nonce,
            'code_challenge' => $this->base64Url(hash('sha256', $verifier, true)),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return [
            'url' => $metadata['authorization_endpoint'].'?'.$query,
            'attempt' => compact('state', 'nonce', 'verifier', 'mode', 'userId') + ['createdAt' => time()],
        ];
    }

    public function finish(string $code, string $state, array $attempt): array
    {
        if (!$code || !$state || !isset($attempt['state'], $attempt['nonce'], $attempt['verifier'], $attempt['createdAt'])
            || time() - (int) $attempt['createdAt'] > 300 || !hash_equals($attempt['state'], $state)) {
            throw new \RuntimeException('La respuesta de identidad no es válida o expiró.');
        }
        $metadata = $this->metadata();
        $tokens = $this->httpClient->request('POST', $metadata['token_endpoint'], ['body' => [
            'grant_type' => 'authorization_code', 'code' => $code,
            'client_id' => $this->oidcClientId, 'client_secret' => $this->oidcClientSecret,
            'redirect_uri' => $this->oidcRedirectUri, 'code_verifier' => $attempt['verifier'],
        ]])->toArray();
        if (!isset($tokens['id_token'], $tokens['access_token'], $tokens['refresh_token'])) {
            throw new \RuntimeException('El proveedor de identidad no devolvió todos los tokens requeridos.');
        }
        $jwks = $this->httpClient->request('GET', $metadata['jwks_uri'])->toArray();
        $claims = (array) JWT::decode($tokens['id_token'], JWK::parseKeySet($jwks));
        $this->validateClaims($claims, $attempt['nonce']);
        $access = (array) JWT::decode($tokens['access_token'], JWK::parseKeySet($jwks));
        $this->validateClaims($access, null);
        if (isset($access['typ']) && !in_array($access['typ'], ['Bearer', 'JWT'], true)) {
            throw new \RuntimeException('El tipo del access token no está permitido.');
        }
        return [
            'issuer' => $claims['iss'], 'subject' => $claims['sub'],
            'idToken' => $tokens['id_token'], 'refreshToken' => $tokens['refresh_token'],
            'accessExpiresAt' => (int) $access['exp'], 'mode' => $attempt['mode'] ?? 'login',
            'userId' => $attempt['userId'] ?? null,
        ];
    }

    public function refresh(string $refreshToken): array
    {
        $metadata = $this->metadata();
        $tokens = $this->httpClient->request('POST', $metadata['token_endpoint'], ['body' => [
            'grant_type' => 'refresh_token', 'refresh_token' => $refreshToken,
            'client_id' => $this->oidcClientId, 'client_secret' => $this->oidcClientSecret,
        ]])->toArray();
        if (!isset($tokens['access_token'])) {
            throw new \RuntimeException('No fue posible renovar la sesión institucional.');
        }
        $jwks = $this->httpClient->request('GET', $metadata['jwks_uri'])->toArray();
        $access = (array) JWT::decode($tokens['access_token'], JWK::parseKeySet($jwks));
        $this->validateClaims($access, null);

        return [
            'refreshToken' => $tokens['refresh_token'] ?? $refreshToken,
            'accessExpiresAt' => (int) $access['exp'],
            'idToken' => $tokens['id_token'] ?? null,
        ];
    }

    public function logoutUrl(string $idToken): string
    {
        $endpoint = $this->metadata()['end_session_endpoint'] ?? null;
        if (!$endpoint) { return $this->oidcPostLogoutRedirectUri; }
        return $endpoint.'?'.http_build_query([
            'id_token_hint' => $idToken, 'client_id' => $this->oidcClientId,
            'post_logout_redirect_uri' => $this->oidcPostLogoutRedirectUri,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function metadata(): array
    {
        $metadata = $this->httpClient->request('GET', rtrim($this->oidcIssuerUrl, '/').'/.well-known/openid-configuration')->toArray();
        if (($metadata['issuer'] ?? null) !== $this->oidcIssuerUrl) {
            throw new \RuntimeException('El emisor OIDC no coincide con la configuración.');
        }
        foreach (['authorization_endpoint', 'token_endpoint', 'jwks_uri'] as $required) {
            if (empty($metadata[$required])) { throw new \RuntimeException('La configuración OIDC está incompleta.'); }
        }
        return $metadata;
    }

    private function validateClaims(array $claims, ?string $nonce): void
    {
        if (($claims['iss'] ?? null) !== $this->oidcIssuerUrl || empty($claims['sub'])) {
            throw new \RuntimeException('El token no pertenece al emisor configurado.');
        }
        $audiences = (array) ($claims['aud'] ?? []);
        if (!in_array($this->oidcExpectedAudience, $audiences, true)) {
            throw new \RuntimeException('El token no está destinado a SGU.');
        }
        if (count($audiences) > 1 && ($claims['azp'] ?? null) !== $this->oidcClientId) {
            throw new \RuntimeException('El cliente autorizado del token no coincide.');
        }
        if (!isset($claims['iat']) || (int) $claims['iat'] > time() + 60) {
            throw new \RuntimeException('La fecha de emisión del token no es válida.');
        }
        if ($nonce !== null && (!isset($claims['nonce']) || !hash_equals($nonce, (string) $claims['nonce']))) {
            throw new \RuntimeException('El nonce de autenticación no coincide.');
        }
    }

    private function randomValue(int $bytes = 32): string { return $this->base64Url(random_bytes($bytes)); }
    private function base64Url(string $value): string { return rtrim(strtr(base64_encode($value), '+/', '-_'), '='); }
}
