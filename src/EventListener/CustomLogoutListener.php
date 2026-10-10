<?php

namespace App\EventListener;

use App\Entity\NotificacionesUsuario;
use App\Repository\Personal\ResponsableRepository;
use App\Services\TraceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Serializer\SerializerInterface;
use App\Security\OidcClient;

class CustomLogoutListener
{

    private $router;
    private $entityManager;
    private $tokenStorage;
    private $requestStack;
    private $serializer;
    private OidcClient $oidc;
    private TraceService $traceService;
    private string $accionCierreSesion;
    private string $objetoAutenticacion;
    private string $tipoTrazaSesion;

    /**
     * CustomLogoutListener constructor.
     * @param RouterInterface $router
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     * @param SerializerInterface $serializer
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, RequestStack $requestStack, SerializerInterface $serializer, OidcClient $oidc, TraceService $traceService, string $accionCierreSesion, string $objetoAutenticacion, string $tipoTrazaSesion)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
        $this->oidc = $oidc;
        $this->traceService = $traceService;
        $this->accionCierreSesion = $accionCierreSesion;
        $this->objetoAutenticacion = $objetoAutenticacion;
        $this->tipoTrazaSesion = $tipoTrazaSesion;
    }

    /**
     * @param LogoutEvent $logoutEvent
     * @return void
     */
    #[NoReturn]
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {

        $this->traceService->registrar($this->accionCierreSesion, $this->objetoAutenticacion, null, null, $this->tipoTrazaSesion);

        $session = $logoutEvent->getRequest()->getSession();
        $idToken = $session->get('oidc_id_token');
        if ($session->get('oidc_authenticated') && is_string($idToken) && $idToken !== '') {
            try {
                $logoutEvent->setResponse(new RedirectResponse($this->oidc->logoutUrl($idToken)));
            } catch (\Throwable $exception) {
                // Local logout still succeeds when the identity provider is temporarily unavailable.
            }
        }


//        $notificacion = new NotificacionesUsuario();
//        $notificacion->setUsuarioEnvia($this->tokenStorage->getToken()->getUser());
//        $notificacion->setUsuarioRecive($this->tokenStorage->getToken()->getUser());
//        $notificacion->setFechaCreado(new \DateTime());
//        $notificacion->setLeido(false);
//        $notificacion->setTexto('El usuario ' . $this->tokenStorage->getToken()->getUser()->getUserIdentifier() . ' ha cerrado el sistema.');
//
//        $url = $this->router->generate("app_todo_lista_cliente", [], UrlGeneratorInterface::ABSOLUTE_URL);
//
//        $notificacion->setUrl($url);
//
//        $this->entityManager->persist($notificacion);
//        $this->entityManager->flush();
    }
}
