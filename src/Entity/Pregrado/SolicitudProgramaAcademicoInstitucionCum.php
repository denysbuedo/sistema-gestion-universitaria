<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Institucion\InstitucionCum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbr_solicitud_programa_academico_institucion_cum')]
#[ORM\Entity]
class SolicitudProgramaAcademicoInstitucionCum extends BaseEntity
{

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Institucion\InstitucionCum::class)]
    private ?InstitucionCum $institucionCum;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion::class)]
    private ?SolicitudProgramaAcademicoInstitucion $solicitudProgramaAcademicoInstitucion;


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $modalidad = null;

    public function getInstitucionCum(): ?InstitucionCum
    {
        return $this->institucionCum;
    }

    public function setInstitucionCum(?InstitucionCum $institucionCum): void
    {
        $this->institucionCum = $institucionCum;
    }

    public function getSolicitudProgramaAcademicoInstitucion(): ?SolicitudProgramaAcademicoInstitucion
    {
        return $this->solicitudProgramaAcademicoInstitucion;
    }

    public function setSolicitudProgramaAcademicoInstitucion(?SolicitudProgramaAcademicoInstitucion $solicitudProgramaAcademicoInstitucion): void
    {
        $this->solicitudProgramaAcademicoInstitucion = $solicitudProgramaAcademicoInstitucion;
    }

    public function getModalidad(): ?string
    {
        return $this->modalidad;
    }

    public function setModalidad(?string $modalidad): void
    {
        $this->modalidad = $modalidad;
    }




}
