<?php

namespace App\Repository\Tramite;

use App\Entity\Tramite\FichaSalidaHistorialDocumento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FichaSalidaHistorialDocumento>
 *
 * @method FichaSalidaHistorialDocumento|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichaSalidaHistorialDocumento|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichaSalidaHistorialDocumento[]    findAll()
 * @method FichaSalidaHistorialDocumento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichaSalidaHistorialDocumentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichaSalidaHistorialDocumento::class);
    }
}
