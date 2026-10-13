<?php

namespace App\Repository\Informatizacion;

use App\Entity\Informatizacion\TelefonoCelular;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TelefonoCelular>
 *
 * @method TelefonoCelular|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelefonoCelular|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelefonoCelular[]    findAll()
 * @method TelefonoCelular[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelefonoCelularRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelefonoCelular::class);
    }

    public function add(TelefonoCelular $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(TelefonoCelular $entity, bool $flush = true): TelefonoCelular
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(TelefonoCelular $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCountByMarca(): array
    {
        $result = $this->createQueryBuilder('tc')
            ->select('m.nombre AS name, COUNT(tc.id) AS y')
            ->innerJoin('tc.marca', 'm')
            ->groupBy('m.id, m.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }

    public function getCountBySistemaOperativo(): array
    {
        $result = $this->createQueryBuilder('tc')
            ->select('so.nombre AS name, COUNT(tc.id) AS y')
            ->innerJoin('tc.sistemaOperativo', 'so')
            ->groupBy('so.id, so.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }
}
