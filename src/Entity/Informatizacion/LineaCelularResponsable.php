<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseEntity;
use \App\Entity\Informatizacion\LineaCelular;
use App\Entity\Personal\Persona;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'informatizacion.tbr_linea_celular_responsable')]
#[ORM\Entity]
class LineaCelularResponsable extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: LineaCelular::class)]
    private ?LineaCelular $lineaCelular;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $responsable;

    /**
     * @return \App\Entity\Informatizacion\LineaCelular|null
     */
    public function getLineaCelular(): ?\App\Entity\Informatizacion\LineaCelular
    {
        return $this->lineaCelular;
    }

    /**
     * @param \App\Entity\Informatizacion\LineaCelular|null $lineaCelular
     */
    public function setLineaCelular(?\App\Entity\Informatizacion\LineaCelular $lineaCelular): void
    {
        $this->lineaCelular = $lineaCelular;
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
