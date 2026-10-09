<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="institucion.tbr_institucion_proyecto")
 */
class InstitucionProyecto extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Institucion")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?Institucion $institucion;


    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $idProyecto = null;


    /**
     * @ORM\Column(type="string", nullable=false, length="255")
     */
    private ?string $nombreProyecto = null;


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
     * @return int|null
     */
    public function getIdProyecto(): ?int
    {
        return $this->idProyecto;
    }

    /**
     * @param int|null $idProyecto
     */
    public function setIdProyecto(?int $idProyecto): void
    {
        $this->idProyecto = $idProyecto;
    }

    /**
     * @return string|null
     */
    public function getNombreProyecto(): ?string
    {
        return $this->nombreProyecto;
    }

    /**
     * @param string|null $nombreProyecto
     */
    public function setNombreProyecto(?string $nombreProyecto): void
    {
        $this->nombreProyecto = $nombreProyecto;
    }






}
