<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'personal.tbn_organizacion')]
#[ORM\Entity]
#[UniqueEntity(fields: 'siglas', message: 'EL valor {{ value }} ya existe. Por favor inserte otro valor.')]
class Organizacion extends BaseNomenclator
{    
    #[ORM\Column(type: 'string', length: 10)]
    private $siglas;

     #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: TipoOrganizacion::class)]
    private ?TipoOrganizacion $tipoOrganizacion;

    public function getSiglas(): ?string
    {
        return $this->siglas;
    }

    public function setSiglas(string $siglas): self
    {
        $this->siglas = $siglas;

        return $this;
    }

    public function getTipoOrganizacion()
    {
        return $this->tipoOrganizacion;
    }
   
    public function setTipoOrganizacion($tipoOrganizacion)
    {
        $this->tipoOrganizacion = $tipoOrganizacion;

        return $this;
    }
}
