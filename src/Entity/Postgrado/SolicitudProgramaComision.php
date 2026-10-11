<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbr_solicitud_programa_comision')]
#[ORM\Entity]
class SolicitudProgramaComision extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Comision::class)]
    private ?Comision $comision = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: SolicitudPrograma::class)]
    private ?SolicitudPrograma $solicitudPrograma = null;


    public function getComision()
    {
        return $this->comision;
    }

    public function setComision($comision)
    {
        $this->comision = $comision;

        return $this;
    }


    public function getSolicitudPrograma()
    {
        return $this->solicitudPrograma;
    }

    public function setSolicitudPrograma($solicitudPrograma)
    {
        $this->solicitudPrograma = $solicitudPrograma;

        return $this;
    }


}
