<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use App\Entity\Personal\Persona;
use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbr_miembros_comision')]
#[ORM\Entity]
class MiembrosComision extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Comision::class)]
    private ?Comision $comision;
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $miembro;


    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: RolComision::class)]
    private ?RolComision $rolComision;


    public function getMiembro()
    {
        return $this->miembro;
    }

    public function setMiembro($miembro)
    {
        $this->miembro = $miembro;

        return $this;
    }

    public function getRolComision()
    {
        return $this->rolComision;
    }

    public function setRolComision($rolComision)
    {
        $this->rolComision = $rolComision;

        return $this;
    }

    public function getComision()
    {
        return $this->comision;
    }

    public function setComision($comision)
    {
        $this->comision = $comision;

        return $this;
    }
}
