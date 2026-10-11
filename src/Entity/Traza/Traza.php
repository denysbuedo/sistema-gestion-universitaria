<?php

namespace App\Entity\Traza;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Personal\Persona;
use App\Doctrine\Attribute\Timestampable;

#[ORM\Table(name: 'traza.tbd_traza')]
#[ORM\Entity]
class Traza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    protected $creado;

    #[Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    protected $actualizado;


    #[ORM\Column(type: 'string', length: 255)]
    private $ip;

    #[ORM\Column(type: 'string', length: 255)]
    private $navegador;

    #[ORM\Column(type: 'string', length: 255)]
    private $sistemaOperativo;

    #[ORM\Column(type: 'text')]
    private $data;

    #[ORM\Column(type: 'text', nullable: true)]
    private $dataAnterior;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: TipoTraza::class)]
    private ?TipoTraza $tipoTraza;


    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Accion::class)]
    private ?Accion $accion;


    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Objeto::class)]
    private ?Objeto $objeto;


    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $persona;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreado()
    {
        return $this->creado;
    }

    public function getActualizado()
    {
        return $this->actualizado;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getNavegador(): ?string
    {
        return $this->navegador;
    }

    public function setNavegador(string $navegador): self
    {
        $this->navegador = $navegador;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }


    public function getTipoTraza()
    {
        return $this->tipoTraza;
    }

    public function setTipoTraza($tipoTraza)
    {
        $this->tipoTraza = $tipoTraza;

        return $this;
    }

    public function getAccion()
    {
        return $this->accion;
    }

    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    public function getObjeto()
    {
        return $this->objeto;
    }

    public function setObjeto($objeto)
    {
        $this->objeto = $objeto;

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

    public function getSistemaOperativo(): ?string
    {
        return $this->sistemaOperativo;
    }

    public function setSistemaOperativo(string $sistemaOperativo): self
    {
        $this->sistemaOperativo = $sistemaOperativo;

        return $this;
    }

    public function getDataAnterior(): ?string
    {
        return $this->dataAnterior;
    }

    public function setDataAnterior(string $dataAnterior): self
    {
        $this->dataAnterior = $dataAnterior;

        return $this;
    }
}
