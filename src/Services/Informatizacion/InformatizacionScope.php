<?php

namespace App\Services\Informatizacion;

use App\Entity\Estructura\Estructura;
use App\Entity\Personal\Persona;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class InformatizacionScope
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function isGlobal(User $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        foreach ($user->getUserRoles() as $role) {
            if ($role->getActivo() && $role->getRoleKey() === 'ROL_INFORMATIZACION') {
                return true;
            }
        }

        return false;
    }

    public function institution(User $user): ?Estructura
    {
        if ($this->isGlobal($user)) {
            return null;
        }

        $person = $this->entityManager->getRepository(Persona::class)->findOneBy(['usuario' => $user->getId()]);
        $institution = $person?->getEntidad();
        if (!$institution instanceof Estructura) {
            throw new AccessDeniedHttpException('Su usuario no está asociado a una universidad.');
        }

        return $institution;
    }

    public function criteria(User $user): array
    {
        $institution = $this->institution($user);

        return $institution ? ['estructura' => $institution] : [];
    }

    public function assertCanManage(User $user, object $resource): void
    {
        $institution = $this->institution($user);
        if ($institution === null) {
            return;
        }

        if (!method_exists($resource, 'getEstructura') || $resource->getEstructura()?->getId() !== $institution->getId()) {
            throw new AccessDeniedHttpException('No tiene permisos para gestionar información de otra universidad.');
        }
    }

    public function assertCanAssignPerson(User $user, Persona $person): void
    {
        $institution = $this->institution($user);
        if ($institution !== null && $person->getEntidad()?->getId() !== $institution->getId()) {
            throw new AccessDeniedHttpException('No puede asignar una persona perteneciente a otra universidad.');
        }
    }
}
