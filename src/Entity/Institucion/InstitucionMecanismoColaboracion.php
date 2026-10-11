<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbr_institucion_mecanismo_colaboracion')]
#[ORM\Entity]
class InstitucionMecanismoColaboracion extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private ?Institucion $institucion;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idMecanismo = null;


    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombreMecanismo = null;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idTipoMecanismo = null;

    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombreTipoMecanismo = null;


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
    public function getIdMecanismo(): ?int
    {
        return $this->idMecanismo;
    }

    /**
     * @param int|null $idMecanismo
     */
    public function setIdMecanismo(?int $idMecanismo): void
    {
        $this->idMecanismo = $idMecanismo;
    }

    /**
     * @return int|null
     */
    public function getNombreMecanismo(): ?string
    {
        return $this->nombreMecanismo;
    }

    /**
     * @param string|null $nombreMecanismo
     */
    public function setNombreMecanismo(?string $nombreMecanismo): void
    {
        $this->nombreMecanismo = $nombreMecanismo;
    }

    /**
     * @return string|null
     */
    public function getIdTipoMecanismo(): ?int
    {
        return $this->idTipoMecanismo;
    }

    /**
     * @param int|null $idTipoMecanismo
     */
    public function setIdTipoMecanismo(?int $idTipoMecanismo): void
    {
        $this->idTipoMecanismo = $idTipoMecanismo;
    }

    /**
     * @return string|null
     */
    public function getNombreTipoMecanismo(): ?string
    {
        return $this->nombreTipoMecanismo;
    }

    /**
     * @param string|null $nombreTipoMecanismo
     */
    public function setNombreTipoMecanismo(?string $nombreTipoMecanismo): void
    {
        $this->nombreTipoMecanismo = $nombreTipoMecanismo;
    }





}
