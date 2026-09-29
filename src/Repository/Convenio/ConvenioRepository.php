<?php

namespace App\Repository\Convenio;

use App\Entity\Convenio\Convenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Convenio>
 *
 * @method Convenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Convenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Convenio[]    findAll()
 * @method Convenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConvenioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Convenio::class);
    }

    public function add(Convenio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(Convenio $entity, bool $flush = true): Convenio
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(Convenio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByIdEntidad(int $idEntidad)
    {
        $response = $this->createQueryBuilder('c')
            ->innerJoin('c.creadoPor', 'p')
            ->innerJoin('p.entidad', 'e')
            ->where('e.id = :idEntidad')
            ->setParameter('idEntidad', $idEntidad)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
        return $response;
    }

    public function getConveniosVigentes()
    {
        $fechaActual = new \DateTime();
        return $this->createQueryBuilder('c')
            ->where('c.fechaCaducidad >= :fechaActual')
            ->setParameter('fechaActual', $fechaActual->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getConveniosVencidos()
    {
        $fechaActual = new \DateTime();
        return $this->createQueryBuilder('c')
            ->where('c.fechaCaducidad < :fechaActual')
            ->setParameter('fechaActual', $fechaActual->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getConveniosProximosVencer(int $dias = 90)
    {
        $fechaActual = new \DateTime();
        $fechaLimite = (new \DateTime())->add(new \DateInterval("P{$dias}D"));
        
        return $this->createQueryBuilder('c')
            ->where('c.fechaCaducidad >= :fechaActual')
            ->andWhere('c.fechaCaducidad <= :fechaLimite')
            ->setParameter('fechaActual', $fechaActual->format('Y-m-d'))
            ->setParameter('fechaLimite', $fechaLimite->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getConveniosPorTipo()
    {
        $data = $this->createQueryBuilder('c')
            ->innerJoin('c.tipo', 't')
            ->select('t.nombre as name', 'COUNT(c.id) as y')
            ->groupBy('t.id', 't.nombre')
            ->orderBy('y', 'DESC')
            ->getQuery()
            ->getResult();
        
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'name' => (string)$item['name'],
                'y' => (int)$item['y']
            ];
        }
        
        return $formattedData;
    }

    public function getConveniosPorPais()
    {
        $data = $this->createQueryBuilder('c')
            ->innerJoin('c.pais', 'p')
            ->select('p.nombre as name', 'COUNT(c.id) as y')
            ->groupBy('p.id', 'p.nombre')
            ->orderBy('y', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'name' => (string)$item['name'],
                'y' => (int)$item['y']
            ];
        }
        
        return $formattedData;
    }

    public function getConveniosPorAnio()
    {
        $sql = "SELECT EXTRACT(YEAR FROM c.fecha_suscribe) as anio, COUNT(c.id) as y 
                FROM convenio.tbd_convenio c 
                WHERE c.fecha_suscribe IS NOT NULL 
                GROUP BY anio 
                ORDER BY anio DESC 
                LIMIT 10";
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();
        $data = $result->fetchAllAssociative();
        
        // Convertir a formato compatible con Highcharts
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'name' => (string)$item['anio'],
                'y' => (int)$item['y']
            ];
        }
        
        return $formattedData;
    }

    public function getConveniosPorInstitucionCubana()
    {
        $data = $this->createQueryBuilder('c')
            ->innerJoin('c.institucionCubana', 'i')
            ->select('i.nombre as name', 'COUNT(c.id) as y')
            ->groupBy('i.id', 'i.nombre')
            ->orderBy('y', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        
        // Convertir a formato compatible con JSON
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'name' => (string)$item['name'],
                'y' => (int)$item['y']
            ];
        }
        
        return $formattedData;
    }

    public function getConveniosPorInstitucionExtranjera()
    {
        $data = $this->createQueryBuilder('c')
            ->innerJoin('c.institucionExtranjera', 'i')
            ->select('i.nombre as name', 'COUNT(c.id) as y')
            ->groupBy('i.id', 'i.nombre')
            ->orderBy('y', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        
        // Convertir a formato compatible con JSON
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'name' => (string)$item['name'],
                'y' => (int)$item['y']
            ];
        }
        
        return $formattedData;
    }

}
