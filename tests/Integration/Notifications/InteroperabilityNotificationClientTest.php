<?php

namespace App\Tests\Integration\Notifications;

use App\Integration\Notifications\InteroperabilityNotificationClient;
use App\Integration\Notifications\NotificationIntegrationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class InteroperabilityNotificationClientTest extends TestCase
{
    public function testChecksAudienceAndRoleWithoutExposingToken(): void
    {
        $token = $this->token([
            'aud' => ['mes-api'],
            'azp' => 'sgu',
            'realm_access' => ['roles' => ['notification-sender']],
        ]);
        $client = new InteroperabilityNotificationClient(
            new MockHttpClient(new MockResponse(json_encode(['access_token' => $token, 'expires_in' => 300], JSON_THROW_ON_ERROR), ['http_code' => 200])),
            false,
            'https://identidad.example/token',
            'sgu',
            'secret',
            '',
            'mes-api',
            'notification-sender',
        );

        $result = $client->checkAuthentication();

        self::assertSame('sgu', $result['client_id']);
        self::assertSame('mes-api', $result['audience']);
        self::assertSame('notification-sender', $result['role']);
        self::assertArrayNotHasKey('access_token', $result);
    }

    public function testRejectsSendingWhileIntegrationIsDisabled(): void
    {
        $client = new InteroperabilityNotificationClient(
            new MockHttpClient(),
            false,
            'https://identidad.example/token',
            'sgu',
            'secret',
            '',
            'mes-api',
            'notification-sender',
        );

        $this->expectException(NotificationIntegrationException::class);
        $client->send(['message' => 'test']);
    }

    /** @param array<string,mixed> $claims */
    private function token(array $claims): string
    {
        $encode = static fn (array $value): string => rtrim(strtr(base64_encode(json_encode($value, JSON_THROW_ON_ERROR)), '+/', '-_'), '=');

        return $encode(['alg' => 'none']) . '.' . $encode($claims) . '.signature';
    }
}
