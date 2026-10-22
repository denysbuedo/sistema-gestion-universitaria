<?php

namespace App\Repository\Security;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): User
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(User $entity, bool $flush = true): User
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->findOneBy(['email' => $identifier]);
        if (!$user instanceof User) {
            throw new \Symfony\Component\Security\Core\Exception\UserNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $refreshed = $this->findOneBy(['email' => $user->getUserIdentifier()]);
        if (!$refreshed instanceof User) {
            throw new \Symfony\Component\Security\Core\Exception\UserNotFoundException();
        }

        return $refreshed;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function getUsuarios()
    {
        $query = "select
            seguridad.tbd_usuario.id,
            tbd_persona.primer_nombre,
            tbd_persona.segundo_nombre,
            tbd_persona.primer_apellido,
            tbd_persona.segundo_apellido,
            tbd_persona.foto,
            seguridad.tbd_usuario.email
            from  seguridad.tbd_usuario
            left join personal.tbd_persona on personal.tbd_persona.usuario_id = seguridad.tbd_usuario.id 
            order by tbd_persona.primer_nombre";

        $connect = $this->getEntityManager()->getConnection();
        $temp = $connect->executeQuery($query);
        return $temp->fetchAllAssociative();
    }

    public function countGroupedByRole(): array
    {
        return $this->createQueryBuilder('u')
            ->select('r.nombre AS label', 'COUNT(DISTINCT u.id) AS value')
            ->innerJoin('u.userRoles', 'r')
            ->groupBy('r.id', 'r.nombre')
            ->getQuery()
            ->getArrayResult();
    }
}
