<?php

namespace App\Repository\Postgrado;

use App\Entity\Postgrado\SolicitudProgramaHistorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SolicitudProgramaHistorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, SolicitudProgramaHistorial::class); }
    public function add(SolicitudProgramaHistorial $entity): void { $this->getEntityManager()->persist($entity); $this->getEntityManager()->flush(); }
}
