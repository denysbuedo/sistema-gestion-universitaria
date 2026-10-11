<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Postgrado\Comision;
use App\Entity\Postgrado\SolicitudPrograma;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbr_solicitud_programa_academico_plan_estudio')]
#[ORM\Entity]
class SolicitudProgramaAcademicoPlanEstudio extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\PlanEstudio::class)]
    private ?PlanEstudio $planEstudio = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\SolicitudProgramaAcademico::class)]
    private ?SolicitudProgramaAcademico $solicitudProgramaAcademico = null;

    /**
     * @return PlanEstudio|null
     */
    public function getPlanEstudio(): ?PlanEstudio
    {
        return $this->planEstudio;
    }

    /**
     * @param PlanEstudio|null $planEstudio
     */
    public function setPlanEstudio(?PlanEstudio $planEstudio): void
    {
        $this->planEstudio = $planEstudio;
    }

    /**
     * @return SolicitudProgramaAcademico|null
     */
    public function getSolicitudProgramaAcademico(): ?SolicitudProgramaAcademico
    {
        return $this->solicitudProgramaAcademico;
    }

    /**
     * @param SolicitudProgramaAcademico|null $solicitudProgramaAcademico
     */
    public function setSolicitudProgramaAcademico(?SolicitudProgramaAcademico $solicitudProgramaAcademico): void
    {
        $this->solicitudProgramaAcademico = $solicitudProgramaAcademico;
    }


}
