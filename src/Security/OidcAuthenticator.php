<?php

namespace App\Security;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class OidcAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private OidcClient $oidc,
        private EntityManagerInterface $entityManager,
        private UserSessionInitializer $sessionInitializer
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oidc_callback';
    }

    public function authenticate(Request $request): Passport
    {
        $session = $request->getSession();
        $attempt = (array) $session->remove('oidc_attempt');
        try {
            $identity = $this->oidc->finish((string) $request->query->get('code'), (string) $request->query->get('state'), $attempt);
            $user = $this->resolveUser($identity);
            $session->set('oidc_id_token', $identity['idToken']);
            $session->set('oidc_authenticated', true);
            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier(), fn () => $user));
        } catch (\Throwable $exception) {
            throw new AuthenticationException('No se pudo completar la autenticación institucional.', 0, $exception);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $request->getSession()->migrate(true);
        $this->sessionInitializer->initialize($token->getUser());
        return new RedirectResponse('/');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->getFlashBag()->add('error', 'No se pudo iniciar sesión con la identidad institucional.');
        return new RedirectResponse('/login');
    }

    private function resolveUser(array $identity): User
    {
        $repository = $this->entityManager->getRepository(User::class);
        if ($identity['mode'] === 'link') {
            $user = $repository->find($identity['userId']);
            if (!$user instanceof User) { throw new \RuntimeException('La sesión local ya no está disponible.'); }
            $other = $repository->findOneBy(['oidcIssuer' => $identity['issuer'], 'oidcSubject' => $identity['subject']]);
            if ($other && $other->getId() !== $user->getId()) { throw new \RuntimeException('La identidad ya está vinculada.'); }
            $user->setOidcIssuer($identity['issuer'])->setOidcSubject($identity['subject']);
            if (in_array('ROLE_ADMIN', $identity['roles'], true)) { $user->setRole('ROLE_ADMIN'); }
            $this->entityManager->flush();
        } else {
            $user = $repository->findOneBy(['oidcIssuer' => $identity['issuer'], 'oidcSubject' => $identity['subject']]);
        }
        if (!$user instanceof User || !$user->getActivo()) {
            throw new \RuntimeException('La identidad no está vinculada a una cuenta activa de SGU.');
        }
        return $user;
    }
}
