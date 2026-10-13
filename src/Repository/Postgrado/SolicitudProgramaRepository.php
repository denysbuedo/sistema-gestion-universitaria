<?php

namespace App\Repository\Postgrado;

use App\Entity\Postgrado\SolicitudPrograma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SolicitudPrograma>
 *
 * @method SolicitudPrograma|null find($id, $lockMode = null, $lockVersion = null)
 * @method SolicitudPrograma|null findOneBy(array $criteria, array $orderBy = null)
 * @method SolicitudPrograma[]    findAll()
 * @method SolicitudPrograma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolicitudProgramaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SolicitudPrograma::class);
    }

    public function add(SolicitudPrograma $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(SolicitudPrograma $entity, bool $flush = true): SolicitudPrograma
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(SolicitudPrograma $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getSolicitudes()
    {
        $qb = $this->createQueryBuilder('qb')
            ->innerJoin('qb.estadoPrograma', 'ep')
            ->where("ep.id NOT IN(:valuesItems)")->setParameter('valuesItems', array_values([7]))
            ->orderBy('qb.id', \SortDirection::Descending);

        $resul = $qb->getQuery()->getResult();
        return $resul;
    }

    public function getSolicitudesSinVotacion()
    {
        // DQL para la subconsulta
        $subQueryDQL = 'SELECT solicitudPrograma.id 
                    FROM App\Entity\Postgrado\SolicitudProgramaVotacion spv
                    JOIN spv.solicitudPrograma solicitudPrograma';

        // Consulta principal
        $qb = $this->createQueryBuilder('solicitud');
        $qb->innerJoin('solicitud.estadoPrograma', 'estadoPrograma')
            ->where('estadoPrograma.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->andWhere($qb->expr()->notIn('solicitud.id', "($subQueryDQL)")) // Uso de la subconsulta DQL
            ->orderBy('solicitud.id', \SortDirection::Descending);

        return $qb->getQuery()->getResult();
    }



    public function getProgramasV2()
    {
        $qb = $this->createQueryBuilder('qb')
            ->select(
                "qb.id,
                        concat('(',e.siglas,') ', e.nombre) as nombre_siglas_organismo,
                        concat('(',c.siglas,') ', c.nombre) as nombre_siglas,
                        qb.nombre,
                        tp.nombre as nombreTipoPrograma,
                        ca.nombre as catAcreditacion,
                         DateFormat(b.fechaEmision, 'DD/MM/YYYY') as fechaEmision,
                         b.numeroPleno,
                         b.numeroAcuerdoPleno,
                         b.annosVigenciaCategoriaAcreditacion")
            ->join('qb.universidad', 'c')
            ->join('qb.tipoPrograma', 'tp')
            ->join('qb.categoriaAcreditacion', 'ca')
            ->join('c.estructura', 'e')
            ->leftJoin('App\Entity\Evaluacion\CategoriaAcreditacionPosgrado', 'b', Join::WITH, 'qb.id = b.solicitudPrograma');

        $qb->orderBy('qb.nombre');
        $resul = $qb->getQuery()->getResult();
        return $resul;
    }

    public function countByEstado(array $estadoIds): int
    {
        return (int) $this->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id IN(:estadoIds)')
            ->setParameter('estadoIds', $estadoIds)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getProgramasPorTipoPrograma(): array
    {
        $result = $this->createQueryBuilder('sp')
            ->select('tp.nombre AS name, COUNT(sp.id) AS y')
            ->innerJoin('sp.tipoPrograma', 'tp')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->groupBy('tp.id, tp.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }

    public function getProgramasPorRamaCiencia(): array
    {
        $result = $this->createQueryBuilder('sp')
            ->select('rc.nombre AS name, COUNT(sp.id) AS y')
            ->innerJoin('sp.ramaCiencia', 'rc')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->groupBy('rc.id, rc.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }

    public function getProgramasPorModalidad(): array
    {
        $result = $this->createQueryBuilder('sp')
            ->select('mp.nombre AS name, COUNT(sp.id) AS y')
            ->innerJoin('sp.modalidadPrograma', 'mp')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->groupBy('mp.id, mp.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }

    public function getProgramasPorUniversidad(): array
    {
        $result = $this->createQueryBuilder('sp')
            ->select('u.nombre AS name, COUNT(sp.id) AS y')
            ->innerJoin('sp.universidad', 'u')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->groupBy('u.id, u.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }

    public function getProgramasPorCategoriaAcreditacion(): array
    {
        $result = $this->createQueryBuilder('sp')
            ->select('ca.nombre AS name, COUNT(sp.id) AS y')
            ->innerJoin('sp.categoriaAcreditacion', 'ca')
            ->innerJoin('sp.estadoPrograma', 'ep')
            ->where('ep.id NOT IN(:excludedIds)')
            ->setParameter('excludedIds', [7])
            ->groupBy('ca.id, ca.nombre')
            ->orderBy('y', \SortDirection::Descending)
            ->getQuery()
            ->getResult();

        return array_map(function ($r) { return ['name' => $r['name'], 'y' => (int)$r['y']]; }, $result);
    }
}
