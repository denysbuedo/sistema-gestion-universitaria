<?php

namespace App\Entity\Estructura;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'estructura.tbn_categoria_responsabilidad')]
#[ORM\Entity]
class CategoriaResponsabilidad extends BaseNomenclator
{
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private string $color;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: CategoriaResponsabilidad::class)]
    private CategoriaResponsabilidad $categoriaResponsabilidad;

    public function getColor()
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCategoriaResponsabilidad()
    {
        return $this->categoriaResponsabilidad;
    }

    public function setCategoriaResponsabilidad($categoriaResponsabilidad)
    {
        $this->categoriaResponsabilidad = $categoriaResponsabilidad;

        return $this;
    }
}
