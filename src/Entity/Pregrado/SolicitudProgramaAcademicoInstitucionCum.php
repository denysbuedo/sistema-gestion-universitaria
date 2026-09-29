<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Institucion\InstitucionCum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pregrado.tbr_solicitud_programa_academico_institucion_cum")
 */
class SolicitudProgramaAcademicoInstitucionCum extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Institucion\InstitucionCum")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?InstitucionCum $institucionCum;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?SolicitudProgramaAcademicoInstitucion $solicitudProgramaAcademicoInstitucion;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
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
