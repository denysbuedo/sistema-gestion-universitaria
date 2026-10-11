<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Institucion\Institucion;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'pregrado.tbr_programa_academico_reabierto_institucion')]
#[ORM\Entity]
class ProgramaAcademicoReabiertoInstitucion extends BaseEntity
{


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Pregrado\ProgramaAcademicoReabierto::class)]
    private ?ProgramaAcademicoReabierto $programaAcademicoReabierto = null;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Institucion\Institucion::class)]
    private ?Institucion $institucion;

    /**
     * @return ProgramaAcademicoReabierto|null
     */
    public function getProgramaAcademicoReabierto(): ?ProgramaAcademicoReabierto
    {
        return $this->programaAcademicoReabierto;
    }

    /**
     * @param ProgramaAcademicoReabierto|null $programaAcademicoReabierto
     */
    public function setProgramaAcademicoReabierto(?ProgramaAcademicoReabierto $programaAcademicoReabierto): void
    {
        $this->programaAcademicoReabierto = $programaAcademicoReabierto;
    }

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



}
