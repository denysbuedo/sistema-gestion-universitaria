<?php

namespace App\Repository\Postgrado;

use App\Entity\Postgrado\SolicitudProgramaDocumento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SolicitudProgramaDocumentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, SolicitudProgramaDocumento::class); }

    public function reemplazar(SolicitudProgramaDocumento $documento): void
    {
        $this->createQueryBuilder('d')->update()->set('d.vigente', ':no')
            ->where('d.solicitudPrograma = :solicitud')->andWhere('d.tipo = :tipo')
            ->setParameter('no', false)->setParameter('solicitud', $documento->getSolicitudPrograma())
            ->setParameter('tipo', $documento->getTipo())->getQuery()->execute();
        $this->getEntityManager()->persist($documento);
        $this->getEntityManager()->flush();
    }
}
