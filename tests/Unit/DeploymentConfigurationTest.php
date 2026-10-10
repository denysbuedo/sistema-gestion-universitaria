<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DeploymentConfigurationTest extends TestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = dirname(__DIR__, 2);
    }

    public function testProductionTemplateContainsEveryDocumentedEnvironmentVariable(): void
    {
        $developmentKeys = $this->environmentKeys($this->projectDir . '/.env.example');
        $productionKeys = $this->environmentKeys($this->projectDir . '/deploy/akademos.env.example');

        self::assertSame([], array_values(array_diff($developmentKeys, $productionKeys)));
        self::assertContains('APP_DEBUG', $productionKeys);
        self::assertStringContainsString('APP_ENV=prod', file_get_contents($this->projectDir . '/deploy/akademos.env.example'));
    }

    public function testNginxAndPhpFpmTemplatesUseTheSameDedicatedSocket(): void
    {
        $nginx = file_get_contents($this->projectDir . '/deploy/nginx/akademos.conf');
        $phpFpm = file_get_contents($this->projectDir . '/deploy/php-fpm/akademos.conf');

        self::assertStringContainsString('/run/php/php8.3-fpm-akademos.sock', $nginx);
        self::assertStringContainsString('/run/php/php8.3-fpm-akademos.sock', $phpFpm);
        self::assertStringContainsString('root /var/www/akademos/current/public;', $nginx);
    }

    public function testUbuntuValidationUsesAnEphemeralTestDatabase(): void
    {
        $compose = file_get_contents($this->projectDir . '/deploy/validation/compose.yaml');
        $dockerfile = file_get_contents($this->projectDir . '/deploy/validation/Dockerfile');

        self::assertStringContainsString('gestion_universitaria_test', $compose);
        self::assertStringContainsString('tmpfs:', $compose);
        self::assertStringContainsString('- /var/lib/postgresql', $compose);
        self::assertStringNotContainsString('/var/lib/postgresql/data', $compose);
        self::assertStringNotContainsString('gestion_universitaria_real', $compose);
        self::assertStringContainsString('cp .env.example .env', $dockerfile);

        $validationScript = file_get_contents($this->projectDir . '/deploy/validation/run.sh');
        self::assertStringContainsString('doctrine:migrations:migrate --env=test', $validationScript);
        self::assertStringContainsString('doctrine:migrations:execute --env=test --down', $validationScript);
        self::assertStringNotContainsString('doctrine:schema:create', $validationScript);
        self::assertStringContainsString('composer audit --locked --format=json', $validationScript);
        self::assertStringContainsString('empty($audit["advisories"])', $validationScript);
        self::assertStringNotContainsString('--abandoned', $validationScript);
    }

    public function testUbuntuWorkflowIsRestrictedAndDoesNotRequireSecrets(): void
    {
        $workflow = file_get_contents($this->projectDir . '/.github/workflows/ubuntu-validation.yml');

        self::assertStringContainsString('runs-on: ubuntu-24.04', $workflow);
        self::assertStringContainsString('contents: read', $workflow);
        self::assertStringContainsString('persist-credentials: false', $workflow);
        self::assertStringNotContainsString('secrets.', $workflow);
    }

    public function testFastWorkflowRunsWithoutDockerOnMainAndDev(): void
    {
        $workflow = file_get_contents($this->projectDir . '/.github/workflows/fast-validation.yml');

        self::assertStringContainsString('branches: [main, dev]', $workflow);
        self::assertStringContainsString('runs-on: ubuntu-24.04', $workflow);
        self::assertStringContainsString('php-version:', $workflow);
        self::assertStringContainsString('composer validate', $workflow);
        self::assertStringContainsString('composer audit', $workflow);
        self::assertStringContainsString('php vendor/bin/phpunit tests/Unit', $workflow);
        self::assertStringContainsString('lint:container', $workflow);
        self::assertStringContainsString("find src config migrations tests -type f -name '*.php'", $workflow);
        self::assertStringNotContainsString('docker', strtolower($workflow));
        self::assertStringNotContainsString('secrets.', $workflow);
    }

    public function testTwoFactorFoundationUsesTotpAndRecoveryCodes(): void
    {
        $twoFactor = file_get_contents($this->projectDir . '/config/packages/scheb_2fa.yaml');
        $security = file_get_contents($this->projectDir . '/config/packages/security.yaml');

        self::assertStringContainsString("totp:\n        enabled: true", $twoFactor);
        self::assertStringContainsString("backup_codes:\n        enabled: true", $twoFactor);
        self::assertStringContainsString('two_factor:', $security);
        self::assertStringContainsString('enable_csrf: true', $security);
        self::assertStringContainsString('IS_AUTHENTICATED_2FA_IN_PROGRESS', $security);
    }

    private function environmentKeys(string $path): array
    {
        $keys = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)=/', $line, $matches)) {
                $keys[] = $matches[1];
            }
        }

        sort($keys);

        return $keys;
    }
}
