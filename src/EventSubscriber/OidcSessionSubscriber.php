<?php

namespace App\EventSubscriber;

use App\Security\OidcClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class OidcSessionSubscriber implements EventSubscriberInterface
{
    public function __construct(private OidcClient $oidc)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['refreshInstitutionalSession', 0]];
    }

    public function refreshInstitutionalSession(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->hasSession() || str_starts_with($request->getPathInfo(), '/oidc/')) {
            return;
        }
        $session = $request->getSession();
        $expiresAt = (int) $session->get('oidc_access_expires_at', 0);
        if (!$session->get('oidc_authenticated') || !$expiresAt || $expiresAt > time() + 60) {
            return;
        }
        $refreshToken = $session->get('oidc_refresh_token');
        if (!is_string($refreshToken) || $refreshToken === '') {
            $this->invalidateAndRedirect($event);
            return;
        }
        try {
            $renewed = $this->oidc->refresh($refreshToken);
            $session->set('oidc_refresh_token', $renewed['refreshToken']);
            $session->set('oidc_access_expires_at', $renewed['accessExpiresAt']);
            if ($renewed['idToken']) {
                $session->set('oidc_id_token', $renewed['idToken']);
            }
        } catch (\Throwable $exception) {
            $this->invalidateAndRedirect($event);
        }
    }

    private function invalidateAndRedirect(RequestEvent $event): void
    {
        $event->getRequest()->getSession()->invalidate();
        $event->setResponse(new RedirectResponse('/login'));
    }
}
