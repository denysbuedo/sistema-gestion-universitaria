<?php

namespace App\Tests\Security;

use App\Security\OidcClient;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class OidcClientTest extends TestCase
{
    private const ISSUER = 'https://identidad.example.test/realms/institucional';

    public function testAuthorizationUsesPkceAndCallbackValidatesSignedTokens(): void
    {
        [$privateKey, $jwk] = $this->keyPair();
        $metadata = $this->metadata();
        $beginClient = $this->client(new MockHttpClient([new MockResponse(json_encode($metadata))]));
        $start = $beginClient->begin('login');
        parse_str(parse_url($start['url'], PHP_URL_QUERY), $query);

        self::assertSame('S256', $query['code_challenge_method']);
        self::assertSame($start['attempt']['state'], $query['state']);
        self::assertSame($start['attempt']['nonce'], $query['nonce']);
        self::assertArrayNotHasKey('code_verifier', $query);

        $claims = [
            'iss' => self::ISSUER, 'sub' => 'stable-subject', 'aud' => 'sgu-dev',
            'iat' => time(), 'exp' => time() + 300, 'nonce' => $start['attempt']['nonce'],
            'roles' => ['ROLE_USER'], 'typ' => 'Bearer',
        ];
        $token = JWT::encode($claims, $privateKey, 'RS256', 'test-key');
        $finishClient = $this->client(new MockHttpClient([
            new MockResponse(json_encode($metadata)),
            new MockResponse(json_encode(['id_token' => $token, 'access_token' => $token])),
            new MockResponse(json_encode(['keys' => [$jwk]])),
        ]));
        $identity = $finishClient->finish('authorization-code', $start['attempt']['state'], $start['attempt']);

        self::assertSame(self::ISSUER, $identity['issuer']);
        self::assertSame('stable-subject', $identity['subject']);
        self::assertSame(['ROLE_USER'], $identity['roles']);
    }

    public function testRejectsMismatchedStateBeforeTokenExchange(): void
    {
        $client = $this->client(new MockHttpClient([new MockResponse(json_encode($this->metadata()))]));
        $start = $client->begin('login');
        $this->expectException(\RuntimeException::class);
        $client->finish('code', 'attacker-state', $start['attempt']);
    }

    private function client(MockHttpClient $http): OidcClient
    {
        return new OidcClient($http, true, self::ISSUER, 'sgu-dev', 'secret',
            'http://127.0.0.1:8000/oidc/check', 'http://127.0.0.1:8000/', 'sgu-dev', 'S256');
    }

    private function metadata(): array
    {
        return [
            'issuer' => self::ISSUER,
            'authorization_endpoint' => self::ISSUER.'/protocol/openid-connect/auth',
            'token_endpoint' => self::ISSUER.'/protocol/openid-connect/token',
            'jwks_uri' => self::ISSUER.'/protocol/openid-connect/certs',
            'end_session_endpoint' => self::ISSUER.'/protocol/openid-connect/logout',
        ];
    }

    private function keyPair(): array
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($key, $private);
        $details = openssl_pkey_get_details($key);
        $encode = static fn (string $value): string => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
        return [$private, [
            'kty' => 'RSA', 'kid' => 'test-key', 'use' => 'sig', 'alg' => 'RS256',
            'n' => $encode($details['rsa']['n']), 'e' => $encode($details['rsa']['e']),
        ]];
    }
}
