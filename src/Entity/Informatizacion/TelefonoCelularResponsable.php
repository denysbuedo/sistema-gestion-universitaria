<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseEntity;
use \App\Entity\Informatizacion\TelefonoCelular;
use App\Entity\Personal\Persona;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'informatizacion.tbr_telefono_celular_responsable')]
#[ORM\Entity]
class TelefonoCelularResponsable extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: TelefonoCelular::class)]
    private ?TelefonoCelular $telefonoCelular;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $responsable;

    /**
     * @return \App\Entity\Informatizacion\TelefonoCelular|null
     */
    public function getTelefonoCelular(): ?\App\Entity\Informatizacion\TelefonoCelular
    {
        return $this->telefonoCelular;
    }

    /**
     * @param \App\Entity\Informatizacion\TelefonoCelular|null $telefonoCelular
     */
    public function setTelefonoCelular(?\App\Entity\Informatizacion\TelefonoCelular $telefonoCelular): void
    {
        $this->telefonoCelular = $telefonoCelular;
    }

    /**
     * @return Persona|null
     */
    public function getResponsable(): ?Persona
    {
        return $this->responsable;
    }

    /**
     * @param Persona|null $responsable
     */
    public function setResponsable(?Persona $responsable): void
    {
        $this->responsable = $responsable;
    }


}
