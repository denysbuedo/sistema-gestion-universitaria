<?php

namespace App\Repository\Tramite;

use App\Entity\Tramite\DocumentoSalidaHistorialDocumento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentoSalidaHistorialDocumento>
 *
 * @method DocumentoSalidaHistorialDocumento|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentoSalidaHistorialDocumento|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentoSalidaHistorialDocumento[]    findAll()
 * @method DocumentoSalidaHistorialDocumento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentoSalidaHistorialDocumentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentoSalidaHistorialDocumento::class);
    }
}
