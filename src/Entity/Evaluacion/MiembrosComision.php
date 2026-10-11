<?php

namespace App\Entity\Evaluacion;

use App\Entity\BaseEntity;
use App\Entity\Personal\Persona;
use App\Entity\BaseNomenclator;
use App\Entity\Evaluacion\RolComision;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evaluacion.tbr_miembros_comision')]
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

    /**
     * @return Comision|null
     */
    public function getComision(): ?Comision
    {
        return $this->comision;
    }

    /**
     * @param Comision|null $comision
     */
    public function setComision(?Comision $comision): void
    {
        $this->comision = $comision;
    }

    /**
     * @return Persona|null
     */
    public function getMiembro(): ?Persona
    {
        return $this->miembro;
    }

    /**
     * @param Persona|null $miembro
     */
    public function setMiembro(?Persona $miembro): void
    {
        $this->miembro = $miembro;
    }

    /**
     * @return \App\Entity\Evaluacion\RolComision|null
     */
    public function getRolComision(): ?\App\Entity\Evaluacion\RolComision
    {
        return $this->rolComision;
    }

    /**
     * @param \App\Entity\Evaluacion\RolComision|null $rolComision
     */
    public function setRolComision(?\App\Entity\Evaluacion\RolComision $rolComision): void
    {
        $this->rolComision = $rolComision;
    }


}
