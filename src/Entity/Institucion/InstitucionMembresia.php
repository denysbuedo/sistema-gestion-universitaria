<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbr_institucion_membresia')]
#[ORM\Entity]
class InstitucionMembresia extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private ?Institucion $institucion;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idMembresia = null;


    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombreMembresia = null;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idTipoMembresia = null;

    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombreTipoMembresia = null;


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
    public function getIdMembresia(): ?int
    {
        return $this->idMembresia;
    }

    /**
     * @param int|null $idMembresia
     */
    public function setIdMembresia(?int $idMembresia): void
    {
        $this->idMembresia = $idMembresia;
    }

    /**
     * @return int|null
     */
    public function getNombreMembresia(): ?string
    {
        return $this->nombreMembresia;
    }

    /**
     * @param string|null $nombreMembresia
     */
    public function setNombreMembresia(?string $nombreMembresia): void
    {
        $this->nombreMembresia = $nombreMembresia;
    }

    /**
     * @return string|null
     */
    public function getIdTipoMembresia(): ?int
    {
        return $this->idTipoMembresia;
    }

    /**
     * @param int|null $idTipoMembresia
     */
    public function setIdTipoMembresia(?int $idTipoMembresia): void
    {
        $this->idTipoMembresia = $idTipoMembresia;
    }

    /**
     * @return string|null
     */
    public function getNombreTipoMembresia(): ?string
    {
        return $this->nombreTipoMembresia;
    }

    /**
     * @param string|null $nombreTipoMembresia
     */
    public function setNombreTipoMembresia(?string $nombreTipoMembresia): void
    {
        $this->nombreTipoMembresia = $nombreTipoMembresia;
    }





}
