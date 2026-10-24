<?php

namespace App\Tests\Functional;

use App\Entity\Security\User;
use App\Entity\Personal\Persona;
use App\Entity\Traza\ConfiguracionTraza;
use App\Entity\Traza\Traza;
use App\Repository\Personal\PersonaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecuritySmokeTest extends WebTestCase
{
    private const TEST_DATABASE = 'gestion_universitaria_test';
    private const BASIC_USER_EMAIL = 'basic-user@example.invalid';
    private const BASIC_USER_PASSWORD = 'basic-user-test-password';
    private const INACTIVE_USER_EMAIL = 'inactive-user@example.invalid';
    private const INACTIVE_USER_PASSWORD = 'inactive-user-test-password';

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

    public function testInactiveUserCannotLoginThroughTheWebForm(): void
    {
        $client = static::createClient();
        $this->getOrCreateInactiveUser($client);
        $csrfToken = $this->getLoginCsrfToken($client);

        $client->request('POST', '/login', [
            'email' => self::INACTIVE_USER_EMAIL,
            'password' => self::INACTIVE_USER_PASSWORD,
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertNull($client->getContainer()->get('security.token_storage')->getToken());
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

    public function testDashboardProvidesTheInstitutionalProcessPortal(): void
    {
        $client = static::createClient();
        $client->loginUser($this->fixtureAdminUser($client), 'main');

        $client->request('GET', '/dashboard');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Gestión universitaria');
        self::assertSelectorTextContains('.section__title', 'Procesos Universitarios');
        self::assertSelectorTextContains('.section__description', 'formación, la investigación, la extensión y la graduación');
        self::assertSelectorExists('#module-search');
        self::assertSelectorExists('[data-module="pregrado formación académica carreras"]');
        self::assertSelectorExists('a[href="/administracion/portada"]');
        self::assertSelectorNotExists('link[href*="fonts.googleapis.com"]');
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

    public function testLocalAdministratorRoleRemainsAuthoritativeAfterUserRefresh(): void
    {
        $client = static::createClient();
        $user = $this->fixtureAdminUser($client);
        self::assertSame('ROLE_ADMIN', $user->getRole());
        $client->loginUser($user, 'main');

        $client->request('GET', '/');
        self::assertResponseRedirects('/dashboard');
        $client->request('GET', '/administracion/usuario/');

        self::assertResponseIsSuccessful();
        self::assertContains('ROLE_ADMIN', $client->getContainer()->get('security.token_storage')->getToken()->getRoleNames());
        $client->getContainer()->get(EntityManagerInterface::class)->clear();
        self::assertSame('ROLE_ADMIN', $this->fixtureAdminUser($client)->getRole());
    }

    public function testLocalBasicRoleRemainsDeniedAfterUserRefresh(): void
    {
        $client = static::createClient();
        $user = $this->getOrCreateBasicUser($client);
        $client->loginUser($user, 'main');

        $client->request('GET', '/');
        self::assertResponseRedirects('/dashboard');
        $client->request('GET', '/administracion/usuario/');

        self::assertResponseStatusCodeSame(403);
        self::assertNotContains('ROLE_ADMIN', $client->getContainer()->get('security.token_storage')->getToken()->getRoleNames());
    }

    /**
     * @dataProvider moduleLandingPageProvider
     */
    public function testAdministratorCanOpenModuleLandingPages(string $path): void
    {
        $client = static::createClient();
        $client->loginUser($this->fixtureAdminUser($client), 'main');

        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider criticalPregradoReadPageProvider
     */
    public function testAdministratorCanOpenCriticalPregradoReadPages(string $path): void
    {
        $client = static::createClient();
        $client->loginUser($this->fixtureAdminUser($client), 'main');

        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
    }

    public function testAdministratorCanOpenTheirPersonEditForm(): void
    {
        $client = static::createClient();
        $user = $this->fixtureAdminUser($client);
        $persona = $client->getContainer()->get(EntityManagerInterface::class)
            ->getRepository(Persona::class)
            ->findOneBy(['usuario' => $user]);
        self::assertInstanceOf(Persona::class, $persona);
        $repository = $client->getContainer()->get(PersonaRepository::class);
        self::assertSame($persona, $repository->edit($persona, false));
        $client->loginUser($user, 'main');

        $client->request('GET', '/personal/persona/' . $persona->getId() . '/modificar');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h3', 'Modificar Registro de Persona');
    }

    public function testNotificationFragmentAndFullPageUseTheirExpectedLayouts(): void
    {
        $client = static::createClient();
        $client->loginUser($this->fixtureAdminUser($client), 'main');

        $client->request('GET', '/notificaciones/usuario');
        self::assertResponseRedirects('/notificaciones/usuario/todas');

        $client->request('POST', '/notificaciones/usuario', server: [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.dropdown-header');

        $client->request('GET', '/notificaciones/usuario/todas');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('link[rel="stylesheet"]');
        self::assertSelectorExists('.content-wrapper');
    }

    public function testLoginAndLogoutCreateSessionTracesWithoutSensitiveData(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $this->fixtureAdminUser($client);
        $persona = $entityManager->getRepository(Persona::class)->findOneBy(['usuario' => $user->getId()]);
        self::assertInstanceOf(Persona::class, $persona);

        $configuration = $entityManager->getRepository(ConfiguracionTraza::class)->findOneBy(['persona' => $persona]);
        if (!$configuration instanceof ConfiguracionTraza) {
            $configuration = (new ConfiguracionTraza())->setPersona($persona);
        }
        $configuration->setActivo(true);
        $entityManager->persist($configuration);
        $entityManager->flush();

        $before = $entityManager->getRepository(Traza::class)->count(['persona' => $persona]);
        $this->loginThroughForm($client);
        $client->request('GET', '/logout');

        $entityManager->clear();
        $traces = $entityManager->getRepository(Traza::class)->findBy(
            ['persona' => $persona->getId()],
            ['id' => 'DESC'],
            2
        );
        self::assertCount(2, $traces);
        self::assertSame($before + 2, $entityManager->getRepository(Traza::class)->count(['persona' => $persona->getId()]));
        self::assertEqualsCanonicalizing(['Inicio de sesión', 'Cierre de sesión'], array_map(
            static fn (Traza $trace): string => $trace->getAccion()->getNombre(),
            $traces
        ));
        foreach ($traces as $trace) {
            $serialized = $trace->getData() . $trace->getDataAnterior();
            self::assertStringNotContainsString($this->fixtureAdminPassword(), $serialized);
            self::assertStringNotContainsString('token', strtolower($serialized));
        }
    }

    public function testAdministratorCanToggleTheirTraceConfiguration(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $this->fixtureAdminUser($client);
        $persona = $entityManager->getRepository(Persona::class)->findOneBy(['usuario' => $user->getId()]);
        self::assertInstanceOf(Persona::class, $persona);

        $configuration = $entityManager->getRepository(ConfiguracionTraza::class)->findOneBy(['persona' => $persona]);
        if (!$configuration instanceof ConfiguracionTraza) {
            $configuration = (new ConfiguracionTraza())->setPersona($persona)->setActivo(true);
            $entityManager->persist($configuration);
            $entityManager->flush();
        }

        $initialState = $configuration->getActivo();
        $client->loginUser($user, 'main');
        $client->request('POST', '/traza/traza/configurar_traza');

        self::assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(!$initialState, $response['enabled']);
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $configuration = $entityManager->getRepository(ConfiguracionTraza::class)->find($configuration->getId());
        self::assertInstanceOf(ConfiguracionTraza::class, $configuration);
        self::assertSame(!$initialState, $configuration->getActivo());

        $client->request('POST', '/traza/traza/configurar_traza');
        self::assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame($initialState, $response['enabled']);
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $configuration = $entityManager->getRepository(ConfiguracionTraza::class)->find($configuration->getId());
        self::assertInstanceOf(ConfiguracionTraza::class, $configuration);
        self::assertSame($initialState, $configuration->getActivo());
    }

    public function testAdministratorCanExportSessionTracesToPdf(): void
    {
        $client = static::createClient();
        $client->loginUser($this->fixtureAdminUser($client), 'main');

        $client->request('GET', '/traza/traza/exportar_pdf');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/pdf');
        self::assertStringStartsWith('%PDF-', $client->getResponse()->getContent());
    }

    public function moduleLandingPageProvider(): iterable
    {
        yield 'administracion' => ['/administracion/portada'];
        yield 'convenio' => ['/convenio/portada'];
        yield 'cooperacion' => ['/cooperacion/portada'];
        yield 'economia' => ['/economia/portada'];
        yield 'estructura' => ['/estructura/portada'];
        yield 'evaluacion' => ['/evaluacion/portada'];
        yield 'informatizacion' => ['/informatizacion/portada'];
        yield 'institucion' => ['/institucion/portada'];
        yield 'personal' => ['/personal/portada'];
        yield 'postgrado' => ['/postgrado/portada'];
        yield 'pregrado' => ['/pregrado/portada'];
        yield 'rrhh' => ['/rrhh/portada'];
        yield 'reporte' => ['/reporte/portada'];
        yield 'tramite' => ['/tramite/portada'];
        yield 'traza' => ['/traza/portada'];
        yield 'visita' => ['/visita/portada'];
    }

    public function criticalPregradoReadPageProvider(): iterable
    {
        yield 'programas aprobados' => ['/pregrado/solicitud_programa_academico_aprobado/'];
        yield 'mis programas asignados' => ['/pregrado/mis_programas/asignados'];
        yield 'mis programas asignados en CUM' => ['/pregrado/mis_programas/asignados_en_cum'];
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

    private function getOrCreateInactiveUser(KernelBrowser $client): User
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => self::INACTIVE_USER_EMAIL,
        ]);

        if (!$user instanceof User) {
            $user = new User();
            $user->setEmail(self::INACTIVE_USER_EMAIL);
            $user->setRole('ROLE_USER');
            $user->setPasswordChangeFirstTime(false);
        }

        $user->setActivo(false);
        $hasher = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, self::INACTIVE_USER_PASSWORD));
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function fixtureAdminUser(KernelBrowser $client): User
    {
        $user = $client->getContainer()->get(EntityManagerInterface::class)
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->fixtureAdminEmail()]);

        self::assertInstanceOf(User::class, $user);

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
