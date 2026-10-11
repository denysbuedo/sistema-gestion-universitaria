<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbr_institucion_recurso_humano')]
#[ORM\Entity]
class InstitucionRecursoHumano extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private ?Institucion $institucion;


    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: RecursosHumanos::class)]
    private ?RecursosHumanos $recursoHumano;

    #[ORM\Column(type: 'integer', nullable: false, length: 100)]
    private ?int $cantidad = null;


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
     * @return Institucion|null
     */
    public function getRecursoHumano(): ?RecursosHumanos
    {
        return $this->recursoHumano;
    }

    /**
     * @param Institucion|null $recursoHumano
     */
    public function setRecursoHumano(?RecursosHumanos $recursoHumano): void
    {
        $this->recursoHumano = $recursoHumano;
    }

    /**
     * @return int|null
     */
    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    /**
     * @param int|null $cantidad
     */
    public function setCantidad(?int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }





}
