<?php

namespace App\Integration\Notifications;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class InteroperabilityNotificationClient
{
    private ?string $accessToken = null;
    private int $accessTokenExpiresAt = 0;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly bool $interopNotificationsEnabled,
        private readonly string $interopNotificationsTokenEndpoint,
        private readonly string $interopNotificationsClientId,
        private readonly string $interopNotificationsClientSecret,
        private readonly string $interopNotificationsEndpoint,
        private readonly string $interopNotificationsExpectedAudience,
        private readonly string $interopNotificationsExpectedRole,
    ) {
    }

    /** @return array{client_id:string,audience:string,role:string,expires_in:int} */
    public function checkAuthentication(): array
    {
        $token = $this->requestAccessToken();

        return [
            'client_id' => $this->interopNotificationsClientId,
            'audience' => $this->interopNotificationsExpectedAudience,
            'role' => $this->interopNotificationsExpectedRole,
            'expires_in' => max(0, $this->accessTokenExpiresAt - time()),
        ];
    }

    /** @param array<string,mixed> $payload
     *  @return array<string,mixed>
     */
    public function send(array $payload): array
    {
        if (!$this->interopNotificationsEnabled) {
            throw new NotificationIntegrationException('El envío de notificaciones de Interoperabilidad está deshabilitado.');
        }
        if (trim($this->interopNotificationsEndpoint) === '') {
            throw new NotificationIntegrationException('No está configurado el endpoint de notificaciones de Interoperabilidad.');
        }

        try {
            $response = $this->httpClient->request('POST', $this->interopNotificationsEndpoint, [
                'auth_bearer' => $this->getAccessToken(),
                'json' => $payload,
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 15,
                'max_duration' => 20,
            ]);
            $status = $response->getStatusCode();
            if ($status < 200 || $status >= 300) {
                throw new NotificationIntegrationException(sprintf('Interoperabilidad rechazó la notificación con HTTP %d.', $status));
            }
            $content = trim($response->getContent(false));
            if ($content === '') {
                return ['status' => $status];
            }
            $decoded = json_decode($content, true);

            return is_array($decoded) ? $decoded : ['status' => $status];
        } catch (NotificationIntegrationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new NotificationIntegrationException('No fue posible entregar la notificación a Interoperabilidad.', 0, $exception);
        }
    }

    /** @return array<string,mixed> */
    public function sendEmail(string $recipient, string $subject, string $textBody): array
    {
        $recipient = trim($recipient);
        $subject = trim($subject);
        $textBody = trim($textBody);
        if ($recipient === '' || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            throw new NotificationIntegrationException('El destinatario de la notificación no es válido.');
        }
        if ($subject === '' || $textBody === '') {
            throw new NotificationIntegrationException('El asunto y el contenido de la notificación son obligatorios.');
        }

        return $this->send([
            'recipient' => $recipient,
            'subject' => $subject,
            'text_body' => $textBody,
        ]);
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken === null || time() >= $this->accessTokenExpiresAt - 30) {
            $this->requestAccessToken();
        }

        return (string) $this->accessToken;
    }

    private function requestAccessToken(): string
    {
        if ($this->interopNotificationsTokenEndpoint === '' || $this->interopNotificationsClientId === '' || $this->interopNotificationsClientSecret === '') {
            throw new NotificationIntegrationException('La autenticación de Interoperabilidad no está completamente configurada.');
        }

        try {
            $response = $this->httpClient->request('POST', $this->interopNotificationsTokenEndpoint, [
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->interopNotificationsClientId,
                    'client_secret' => $this->interopNotificationsClientSecret,
                ],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 10,
                'max_duration' => 15,
            ]);
            $status = $response->getStatusCode();
            $data = $response->toArray(false);
            if ($status !== 200 || !isset($data['access_token']) || !is_string($data['access_token'])) {
                throw new NotificationIntegrationException(sprintf('Identidad rechazó la autenticación del servicio con HTTP %d.', $status));
            }

            $this->validateTokenClaims($data['access_token']);
            $expiresIn = max(1, (int) ($data['expires_in'] ?? 60));
            $this->accessToken = $data['access_token'];
            $this->accessTokenExpiresAt = time() + $expiresIn;

            return $this->accessToken;
        } catch (NotificationIntegrationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new NotificationIntegrationException('No fue posible autenticar SGU ante Interoperabilidad.', 0, $exception);
        }
    }

    private function validateTokenClaims(string $token): void
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new NotificationIntegrationException('Identidad devolvió un token con formato no reconocido.');
        }
        $payload = strtr($parts[1], '-_', '+/');
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
        $decoded = base64_decode($payload, true);
        $claims = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($claims)) {
            throw new NotificationIntegrationException('No fue posible validar los claims del token de servicio.');
        }

        $audiences = isset($claims['aud']) && is_array($claims['aud']) ? $claims['aud'] : [$claims['aud'] ?? null];
        if (!in_array($this->interopNotificationsExpectedAudience, $audiences, true)) {
            throw new NotificationIntegrationException('El token no contiene la audiencia requerida para la API.');
        }

        $roles = [];
        foreach (($claims['realm_access']['roles'] ?? []) as $role) {
            $roles[] = $role;
        }
        foreach (($claims['resource_access'] ?? []) as $resource) {
            foreach (($resource['roles'] ?? []) as $role) {
                $roles[] = $role;
            }
        }
        if (!in_array($this->interopNotificationsExpectedRole, $roles, true)) {
            throw new NotificationIntegrationException('El token no contiene el rol requerido para enviar notificaciones.');
        }
    }
}
