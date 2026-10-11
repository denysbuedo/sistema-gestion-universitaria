<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Personal\Persona;
use App\Entity\Postgrado\RolComision;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbr_miembros_comision_nacional')]
#[ORM\Entity]
class MiembrosComisionNacional extends BaseEntity
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: ComisionNacional::class)]
    private ?ComisionNacional $comision;
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $miembro;


    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Postgrado\RolComision::class)]
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

    /**
     * @return RolComision|null
     */
    public function getRolComision(): ?RolComision
    {
        return $this->rolComision;
    }

    /**
     * @param RolComision|null $rolComision
     */
    public function setRolComision(?RolComision $rolComision): void
    {
        $this->rolComision = $rolComision;
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
