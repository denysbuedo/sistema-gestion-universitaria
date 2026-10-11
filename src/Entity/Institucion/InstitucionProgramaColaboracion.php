<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbr_institucion_programa_colaboracion')]
#[ORM\Entity]
class InstitucionProgramaColaboracion extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private ?Institucion $institucion;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idPrograma = null;


    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombrePrograma = null;


    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $idPatrocinador = null;

    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombrePatrocinador = null;


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
    public function getIdPrograma(): ?int
    {
        return $this->idPrograma;
    }

    /**
     * @param int|null $idPrograma
     */
    public function setIdPrograma(?int $idPrograma): void
    {
        $this->idPrograma = $idPrograma;
    }

    /**
     * @return int|null
     */
    public function getNombrePrograma(): ?string
    {
        return $this->nombrePrograma;
    }

    /**
     * @param string|null $nombrePrograma
     */
    public function setNombrePrograma(?string $nombrePrograma): void
    {
        $this->nombrePrograma = $nombrePrograma;
    }

    /**
     * @return string|null
     */
    public function getIdPatrocinador(): ?int
    {
        return $this->idPatrocinador;
    }

    /**
     * @param int|null $idPatrocinador
     */
    public function setIdPatrocinador(?int $idPatrocinador): void
    {
        $this->idPatrocinador = $idPatrocinador;
    }

    /**
     * @return string|null
     */
    public function getNombrePatrocinador(): ?string
    {
        return $this->nombrePatrocinador;
    }

    /**
     * @param string|null $nombrePatrocinador
     */
    public function setNombrePatrocinador(?string $nombrePatrocinador): void
    {
        $this->nombrePatrocinador = $nombrePatrocinador;
    }


}
