<?php

namespace App\Entity\Estructura;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'estructura.tbn_responsabilidad')]
#[ORM\Entity]
class Responsabilidad  extends BaseNomenclator
{
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: CategoriaResponsabilidad::class)]
    private CategoriaResponsabilidad $categoriaResponsabilidad;

   
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
