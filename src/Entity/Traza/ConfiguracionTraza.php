<?php

namespace App\Entity\Traza;

use App\Entity\Personal\Persona;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'traza.tbd_configuracion_traza')]
#[ORM\Entity]
class ConfiguracionTraza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $activo = true;


    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $persona;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    public function getPersona()
    {
        return $this->persona;
    }

    public function setPersona($persona)
    {
        $this->persona = $persona;

        return $this;
    }

}
