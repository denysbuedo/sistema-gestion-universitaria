<?php

namespace App\Repository\Pregrado;

use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucionCum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SolicitudProgramaAcademicoInstitucionCum>
 *
 * @method SolicitudProgramaAcademicoInstitucionCum|null find($id, $lockMode = null, $lockVersion = null)
 * @method SolicitudProgramaAcademicoInstitucionCum|null findOneBy(array $criteria, array $orderBy = null)
 * @method SolicitudProgramaAcademicoInstitucionCum[]    findAll()
 * @method SolicitudProgramaAcademicoInstitucionCum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolicitudProgramaAcademicoInstitucionCumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SolicitudProgramaAcademicoInstitucionCum::class);
    }

    public function add(SolicitudProgramaAcademicoInstitucionCum $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(SolicitudProgramaAcademicoInstitucionCum $entity, bool $flush = true): SolicitudProgramaAcademicoInstitucionCum
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function remove(SolicitudProgramaAcademicoInstitucionCum $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function misProgramasAsignadosEnCum($institucionId)
    {
        $query = "SELECT 
                c2.nombre as nombre_programa, c1.modalidad, c4.nombre_facultad, c5.nombre as nombre_institucion 
                FROM  pregrado.tbr_solicitud_programa_academico_institucion_cum c1
                join institucion.tbr_institucion_cum c4 on c1.institucion_cum_id = c4.id 
                join institucion.tbd_institucion c5 on c4.institucion_id  = c5.id
                join pregrado.tbr_solicitud_programa_academico_institucion c3 on c1.solicitud_programa_academico_institucion_id = c3.id 
                join pregrado.tbd_solicitud_programa_academico c2 on c3.solicitud_programa_academico_id = c2.id 
                where c3.institucion_id =$institucionId";
        $connect = $this->getEntityManager()->getConnection();
        $temp = $connect->executeQuery($query);
        $temp = $temp->fetchAllAssociative();
        return $temp;
    }

}
