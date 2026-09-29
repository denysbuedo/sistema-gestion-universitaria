<?php

namespace App\Repository\RRHH;

use App\Entity\RRHH\AE3;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AE3|null find($id, $lockMode = null, $lockVersion = null)
 * @method AE3|null findOneBy(array $criteria, array $orderBy = null)
 * @method AE3[]    findAll()
 * @method AE3[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AE3Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AE3::class);
    }

    public function add(AE3 $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function edit(AE3 $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(AE3 $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function findDistinctEntidades(?int $entidadId = null, ?int $mes = null, ?int $anno = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT e.id, e.nombre')
            ->join('a.entidad', 'e')
            ->orderBy('a.mes', 'asc');

        if ($anno !== null) {
            $qb->andWhere('a.anno = :anno')
                ->setParameter('anno', $anno);
        }
        if ($entidadId !== null) {
            $qb->andWhere('e.id = :entidadId')
                ->setParameter('entidadId', $entidadId);
        }
        return $qb->orderBy('e.nombre', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    public function getTabla1_4($mes, $anno)
    {
        return $this->createQueryBuilder('a')
            ->join('a.entidad', 'e')
            ->join('e.tipoEstructura', 'tipoEstructura')
            ->andWhere('a.mes = :mes')
            ->andWhere('a.anno = :anno')
            ->andWhere('e.tipoEstructura NOT IN (:excluded)')
            ->setParameter('mes', $mes)
            ->setParameter('anno', $anno)
            ->setParameter('excluded', [42, 27])
            ->orderBy('e.siglas', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function getLatestPeriod(): ?array
    {
        return $this->createQueryBuilder('a')
            ->select('a.anno, a.mes')
            ->orderBy('a.anno', 'DESC')
            ->addOrderBy('a.mes', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTotalesGlobales(int $anno, int $mes): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT
                COALESCE(SUM(total_total_cubierta),                           0) AS total_cubierta,
                COALESCE(SUM(total_fem),                                      0) AS total_fem,
                COALESCE(SUM(total_jovenes_total),                            0) AS total_jovenes,
                COALESCE(SUM(total_dr),                                       0) AS total_dr,
                COALESCE(SUM(total_msc),                                      0) AS total_msc,
                COALESCE(SUM(total_pt),                                       0) AS total_pt,
                COALESCE(SUM(total_pa),                                       0) AS total_pa
            FROM rrhh.tbd_ae3
            WHERE anno = :anno AND mes = :mes
        ";
        return $conn->executeQuery($sql, ['anno' => $anno, 'mes' => $mes])->fetchAssociative() ?: [];
    }

    public function getDistribucionCategorias(int $anno, int $mes): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT
                COALESCE(SUM(cuadros_docentes_total_cubierta),                0) AS docentes,
                COALESCE(SUM(profesores_tiempo_completo_total_cubierta),      0) AS prof_tc,
                COALESCE(SUM(total_tecnicos_total_cubierta),                  0) AS tecnicos,
                COALESCE(SUM(administrativos_total_cubierta),                 0) AS administrativos,
                COALESCE(SUM(servicio_total_cubierta),                        0) AS servicio,
                COALESCE(SUM(operarios_total_cubierta),                       0) AS operarios
            FROM rrhh.tbd_ae3
            WHERE anno = :anno AND mes = :mes
        ";
        return $conn->executeQuery($sql, ['anno' => $anno, 'mes' => $mes])->fetchAssociative() ?: [];
    }

    public function getTotalDoctoresMasters($entidad, $mes, $anno)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.total_dr, a.total_msc')
            ->where('a.mes = :mes')
            ->andWhere('a.anno = :anno')
            ->andWhere('a.entidad = :entidad')
            ->setParameter('mes', $mes)
            ->setParameter('anno', $anno)
            ->setParameter('entidad', $entidad);

        return $qb->getQuery()->getResult();
    }
}