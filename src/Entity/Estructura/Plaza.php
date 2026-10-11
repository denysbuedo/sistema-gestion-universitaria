<?php

namespace App\Entity\Estructura;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'estructura.tbr_plaza')]
#[ORM\UniqueConstraint(name: 'uniq_estructura_plaza_estructura_responsabilidad', columns: ['estructura_id', 'responsabilidad_id'])]
#[ORM\Entity]
class Plaza extends BaseEntity
{
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    private ?Estructura $estructura;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Responsabilidad::class)]
    private ?Responsabilidad $responsabilidad;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected ?int $cantidad = 0;    
    
    public function getEstructura()
    {
        return $this->estructura;
    }

    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;

        return $this;
    }

    public function getResponsabilidad()
    {
        return $this->responsabilidad;
    }
   
    public function setResponsabilidad($responsabilidad)
    {
        $this->responsabilidad = $responsabilidad;

        return $this;
    }
    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }
}
