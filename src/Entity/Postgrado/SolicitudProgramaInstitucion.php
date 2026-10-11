<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use App\Entity\Institucion\Institucion;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbr_solicitud_programa_institucion')]
#[ORM\Entity]
class SolicitudProgramaInstitucion extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Institucion\Institucion::class)]
    private ?Institucion $institucion = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: SolicitudPrograma::class)]
    private ?SolicitudPrograma $solicitudPrograma = null;

    /**
     * @return Institucion|null
     */
    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    /**
     * @param Institucion|null $institucion
     */
    public function setInstitucion(?Institucion $institucion): void
    {
        $this->institucion = $institucion;
    }

    /**
     * @return SolicitudPrograma|null
     */
    public function getSolicitudPrograma(): ?SolicitudPrograma
    {
        return $this->solicitudPrograma;
    }

    /**
     * @param SolicitudPrograma|null $solicitudPrograma
     */
    public function setSolicitudPrograma(?SolicitudPrograma $solicitudPrograma): void
    {
        $this->solicitudPrograma = $solicitudPrograma;
    }

}
