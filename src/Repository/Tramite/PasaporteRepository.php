<?php

namespace App\Repository\Tramite;

use App\Entity\Tramite\Pasaporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pasaporte>
 *
 * @method Pasaporte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pasaporte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pasaporte[]    findAll()
 * @method Pasaporte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasaporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pasaporte::class);
    }

    public function add(Pasaporte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(Pasaporte $entity, bool $flush = true): Pasaporte
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(Pasaporte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca un pasaporte habilitado (activo=true) para una persona (por CI) y tipo de pasaporte.
     * Retorna null si no existe o si tipoPasaporte es null.
     */
    public function findHabilitadoPorCiYTipo(string $carnetIdentidad, ?object $tipoPasaporte): ?Pasaporte
    {
        if ($tipoPasaporte === null) {
            return null;
        }
        return $this->createQueryBuilder('p')
            ->join('p.persona', 'per')
            ->where('per.carnetIdentidad = :ci')
            ->andWhere('p.tipoPasaporte = :tipo')
            ->andWhere('p.activo = true')
            ->setParameter('ci', $carnetIdentidad)
            ->setParameter('tipo', $tipoPasaporte)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Busca cualquier registro de pasaporte (activo o no) para una persona (por CI) y tipo de pasaporte.
     * Retorna null si no existe o si tipoPasaporte es null.
     */
    public function findCualquieraPorCiYTipo(string $carnetIdentidad, ?object $tipoPasaporte): ?Pasaporte
    {
        if ($tipoPasaporte === null) {
            return null;
        }
        return $this->createQueryBuilder('p')
            ->join('p.persona', 'per')
            ->where('per.carnetIdentidad = :ci')
            ->andWhere('p.tipoPasaporte = :tipo')
            ->setParameter('ci', $carnetIdentidad)
            ->setParameter('tipo', $tipoPasaporte)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Busca un pasaporte pendiente (activo=false) para una persona (por CI) y tipo de pasaporte.
     * Retorna null si no existe o si tipoPasaporte es null.
     */
    public function findPendientePorCiYTipo(string $carnetIdentidad, ?object $tipoPasaporte): ?Pasaporte
    {
        if ($tipoPasaporte === null) {
            return null;
        }
        return $this->createQueryBuilder('p')
            ->join('p.persona', 'per')
            ->where('per.carnetIdentidad = :ci')
            ->andWhere('p.tipoPasaporte = :tipo')
            ->andWhere('p.activo = false')
            ->setParameter('ci', $carnetIdentidad)
            ->setParameter('tipo', $tipoPasaporte)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
