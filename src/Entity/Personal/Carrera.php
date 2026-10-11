<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_carrera')]
#[ORM\Entity]
class Carrera extends BaseNomenclator
{
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\NivelEscolar::class)]
    private ?NivelEscolar $nivelEscolar;

    public function getNivelEscolar()
    {
        return $this->nivelEscolar;
    }

    public function setNivelEscolar($nivelEscolar)
    {
        $this->nivelEscolar = $nivelEscolar;

        return $this;
    }
}
