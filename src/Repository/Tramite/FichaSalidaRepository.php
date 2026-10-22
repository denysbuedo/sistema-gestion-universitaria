<?php

namespace App\Repository\Tramite;

use App\Entity\Tramite\FichaSalida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FichaSalida>
 *
 * @method FichaSalida|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichaSalida|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichaSalida[]    findAll()
 * @method FichaSalida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichaSalidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichaSalida::class);
    }

    public function add(FichaSalida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(FichaSalida $entity, bool $flush = true): FichaSalida
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(FichaSalida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countGroupedByStatus(): array
    {
        return $this->createQueryBuilder('f')
            ->select('IDENTITY(f.estadoFichaSalida) AS id', 'e.nombre AS label', 'COUNT(f.id) AS value')
            ->innerJoin('f.estadoFichaSalida', 'e')
            ->where('e.documentoSalida = false')
            ->andWhere('e.activo = true')
            ->groupBy('f.estadoFichaSalida')
            ->addGroupBy('e.id')
            ->addGroupBy('e.nombre')
            ->getQuery()
            ->getArrayResult();
    }

    public function countByMonth(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $rows = $this->getEntityManager()->getConnection()->executeQuery(
            "SELECT to_char(creado, 'YYYY-MM') AS month, COUNT(*) AS value FROM tramite.tbd_ficha_salida WHERE creado >= :from AND creado < :to GROUP BY to_char(creado, 'YYYY-MM') ORDER BY month",
            ['from' => $from->format('Y-m-d H:i:s'), 'to' => $to->format('Y-m-d H:i:s')]
        )->fetchAllAssociative();

        return array_column($rows, 'value', 'month');
    }
}
