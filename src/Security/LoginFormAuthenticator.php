<?php

namespace App\Security;

use App\Entity\Personal\Persona;
use App\Entity\Personal\PersonaOrganizacion;
use App\Entity\Security\User;
use App\Services\TraceService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $requestStack;
    private $container;
    private TraceService $traceService;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator,
                                RequestStack $requestStack, ContainerInterface $container, TraceService $traceService)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->container = $container;
        $this->traceService = $traceService;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = (string) $request->request->get('email');
        $password = (string) $request->request->get('password');
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $email
        );

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [new CsrfTokenBadge('authenticate', (string) $request->request->get('_csrf_token'))]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        $this->populateUserSession($user);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            if (!$request->getSession()->get(('password_change_first_time')) && !$request->getSession()->get(('usuario_autenticado_role_admin'))) {
                $idUser = $request->getSession()->get(('id_usuario_autenticado'));
                return new RedirectResponse("administracion/cambio_clave/$idUser/ejecutar");
            }
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
//        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        return new RedirectResponse('/');
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function populateUserSession(User $user): void
    {
        $persona = $this->entityManager->getRepository(Persona::class)->findOneBy(['usuario' => $user->getId()]);
        $session = $this->requestStack->getSession();

        if (!$persona instanceof Persona) {
            $session->set('nombre_usuario_autenticado', $user->getUsername());
            $session->set('id_persona_usuario_autenticado', -1);
            $session->set('id_usuario_autenticado', $user->getId());

            return;
        }

        $afiliaciones = $this->entityManager->getRepository(PersonaOrganizacion::class)->findBy(['persona' => $persona->getId()]);
        $siglas = [];
        foreach ($afiliaciones as $afiliacion) {
            $siglas[] = $afiliacion->getOrganizacion()->getSiglas();
        }

        $session->set('id_usuario_autenticado', $user->getId());
        $session->set('usuario_autenticado_role_admin', in_array('ROLE_ADMIN', $user->getRoles(), true));
        $session->set('foto_usuario_autenticado', $persona->getFoto());
        $session->set('nombre_usuario_autenticado', $persona->getPrimerNombre() . ' ' . $persona->getSegundoNombre() . ' ' . $persona->getPrimerApellido() . ' ' . $persona->getSegundoApellido());

        $responsabilidad = $persona->getResponsabilidad();
        $session->set('responsabilidad_usuario_autenticado', $responsabilidad ? $responsabilidad->getNombre() : null);

        $estructura = $persona->getEstructura();
        $estructuraPadre = $estructura ? $estructura->getEstructura() : null;
        $session->set('estructura_usuario_autenticado', $estructura ? $estructura->getId() : null);
        $session->set('nombre_estructura_usuario_autenticado', $estructura ? $estructura->getNombre() : null);
        $session->set('estructura_padre_usuario_autenticado', $estructuraPadre ? $estructuraPadre->getNombre() : null);
        $session->set('id_persona_usuario_autenticado', $persona->getId());
        $session->set('provincia_usuario_autenticado', $persona->getProvincia() ? $persona->getProvincia()->getNombre() : null);
        $session->set('municipio_usuario_autenticado', $persona->getMunicipio() ? $persona->getMunicipio()->getNombre() : null);

        $gradoAcademico = $persona->getGradoAcademico();
        $session->set('grado_cientifico_usuario_autenticado', $gradoAcademico ? $gradoAcademico->getNombre() : null);
        $session->set('afiliaciones_usuario_autenticado', implode(', ', $siglas));
        $session->set('password_change_first_time', $user->getPasswordChangeFirstTime());

        $this->traceService->registrar(
            $this->container->getParameter('accion_inicio_sesion'),
            $this->container->getParameter('objeto_autenticacion'),
            null,
            null,
            $this->container->getParameter('tipo_traza_sesion')
        );
    }
}
