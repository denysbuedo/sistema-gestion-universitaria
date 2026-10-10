<?php

namespace App\Tests\Functional;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecuritySmokeTest extends WebTestCase
{
    private const TEST_DATABASE = 'gestion_universitaria_test';
    private const BASIC_USER_EMAIL = 'basic-user@example.invalid';
    private const BASIC_USER_PASSWORD = 'basic-user-test-password';

    protected function setUp(): void
    {
        parent::setUp();

        $client = static::createClient();
        $database = $client->getContainer()
            ->get(EntityManagerInterface::class)
            ->getConnection()
            ->fetchOne('SELECT current_database()');

        self::assertSame(
            self::TEST_DATABASE,
            $database,
            'La suite se negó a continuar porque no está conectada a la base de pruebas.'
        );

        static::ensureKernelShutdown();
    }

    public function testKernelAndLoginPageStartSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form#login-form');
        self::assertSelectorExists('input[name="_csrf_token"]');
        self::assertSelectorExists('input[name="email"]');
        self::assertSelectorExists('input[name="password"]');
    }

    public function testAnonymousWebAccessIsRedirectedToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');

        self::assertResponseRedirects('/login');
    }

    public function testInvalidWebCredentialsAreRejected(): void
    {
        $client = static::createClient();
        $csrfToken = $this->getLoginCsrfToken($client);

        $client->request('POST', '/login', [
            'email' => $this->fixtureAdminEmail(),
            'password' => 'incorrect-password',
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        $token = $client->getContainer()->get('security.token_storage')->getToken();
        self::assertNull($token);
    }

    public function testValidWebCredentialsCreateSessionAndReachDashboard(): void
    {
        $client = static::createClient();
        $this->loginThroughForm($client);

        self::assertNotNull($client->getContainer()->get('security.token_storage')->getToken());
        self::assertTrue($client->getRequest()->getSession()->get('usuario_autenticado_role_admin'));

        $client->followRedirect();
        self::assertResponseRedirects('/dashboard');
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringEndsWith('/dashboard', $client->getRequest()->getUri());
    }

    public function testLogoutInvalidatesTheAuthenticatedSession(): void
    {
        $client = static::createClient();
        $this->loginThroughForm($client);

        $client->request('GET', '/logout');
        self::assertResponseRedirects('http://localhost/');

        $client->request('GET', '/dashboard');
        self::assertResponseRedirects('/login');
    }

    public function testAnonymousApiAccessIsRejected(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/listar_categorias_estructuras', []);

        self::assertResponseStatusCodeSame(401);
    }

    public function testInvalidJwtCredentialsAreRejected(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/login_check', [
            'email' => $this->fixtureAdminEmail(),
            'password' => 'incorrect-password',
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testValidJwtCanReadAProtectedEndpoint(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/login_check', [
            'email' => $this->fixtureAdminEmail(),
            'password' => $this->fixtureAdminPassword(),
        ]);

        self::assertResponseIsSuccessful();
        $payload = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('token', $payload);
        self::assertNotEmpty($payload['token']);

        $client->jsonRequest(
            'POST',
            '/api/listar_categorias_estructuras',
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $payload['token']]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertIsArray(json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testBasicUserCannotAccessAnAdministratorController(): void
    {
        $client = static::createClient();
        $user = $this->getOrCreateBasicUser($client);
        $client->loginUser($user, 'main');

        $client->request('GET', '/administracion/usuario/');

        self::assertResponseStatusCodeSame(403);
    }

    private function getLoginCsrfToken(KernelBrowser $client): string
    {
        $crawler = $client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        return (string) $crawler->filter('input[name="_csrf_token"]')->attr('value');
    }

    private function loginThroughForm(KernelBrowser $client): void
    {
        $csrfToken = $this->getLoginCsrfToken($client);

        $client->request('POST', '/login', [
            'email' => $this->fixtureAdminEmail(),
            'password' => $this->fixtureAdminPassword(),
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/');
    }

    private function getOrCreateBasicUser(KernelBrowser $client): User
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => self::BASIC_USER_EMAIL,
        ]);

        if ($user instanceof User) {
            return $user;
        }

        $user = new User();
        $user->setEmail(self::BASIC_USER_EMAIL);
        $user->setRole('ROLE_USER');
        $user->setActivo(true);
        $user->setPasswordChangeFirstTime(false);

        $encoder = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $user->setPassword($encoder->hashPassword($user, self::BASIC_USER_PASSWORD));

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function fixtureAdminEmail(): string
    {
        return (string) ($_ENV['FIXTURE_ADMIN_EMAIL'] ?? $_SERVER['FIXTURE_ADMIN_EMAIL']);
    }

    private function fixtureAdminPassword(): string
    {
        return (string) ($_ENV['FIXTURE_ADMIN_PASSWORD'] ?? $_SERVER['FIXTURE_ADMIN_PASSWORD']);
    }
}
