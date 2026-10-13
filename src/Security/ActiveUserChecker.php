<?php

namespace App\Security;

use App\Entity\Security\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ActiveUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && !$user->getActivo()) {
            throw new DisabledException('La cuenta está deshabilitada.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
