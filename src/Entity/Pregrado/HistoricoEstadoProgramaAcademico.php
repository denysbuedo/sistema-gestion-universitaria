<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbr_historico_estado_programa_academico')]
#[ORM\Entity]
class HistoricoEstadoProgramaAcademico extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\SolicitudProgramaAcademico::class)]
    private ?SolicitudProgramaAcademico $solicitudProgramaAcademico = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: EstadoProgramaAcademico::class)]
    private ?EstadoProgramaAcademico $estadoProgramaAcademico;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\CursoAcademico::class)]
    private ?CursoAcademico $cursoAcademico;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $dictamenAprobacion = null;

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

    /**
     * @return EstadoProgramaAcademico|null
     */
    public function getEstadoProgramaAcademico(): ?EstadoProgramaAcademico
    {
        return $this->estadoProgramaAcademico;
    }

    /**
     * @param EstadoProgramaAcademico|null $estadoProgramaAcademico
     */
    public function setEstadoProgramaAcademico(?EstadoProgramaAcademico $estadoProgramaAcademico): void
    {
        $this->estadoProgramaAcademico = $estadoProgramaAcademico;
    }

    /**
     * @return CursoAcademico|null
     */
    public function getCursoAcademico(): ?CursoAcademico
    {
        return $this->cursoAcademico;
    }

    /**
     * @param CursoAcademico|null $cursoAcademico
     */
    public function setCursoAcademico(?CursoAcademico $cursoAcademico): void
    {
        $this->cursoAcademico = $cursoAcademico;
    }

    /**
     * @return string|null
     */
    public function getDictamenAprobacion(): ?string
    {
        return $this->dictamenAprobacion;
    }

    /**
     * @param string|null $dictamenAprobacion
     */
    public function setDictamenAprobacion(?string $dictamenAprobacion): void
    {
        $this->dictamenAprobacion = $dictamenAprobacion;
    }


}
