<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbd_solicitud_programa_documento')]
#[ORM\Entity]
class SolicitudProgramaDocumento extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: SolicitudPrograma::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SolicitudPrograma $solicitudPrograma = null;

    #[ORM\Column(length: 50)]
    private ?string $tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $ruta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombreOriginal = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $vigente = true;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $subidoPor = null;

    public function getSolicitudPrograma(): ?SolicitudPrograma { return $this->solicitudPrograma; }
    public function setSolicitudPrograma(?SolicitudPrograma $value): self { $this->solicitudPrograma = $value; return $this; }
    public function getTipo(): ?string { return $this->tipo; }
    public function setTipo(?string $value): self { $this->tipo = $value; return $this; }
    public function getRuta(): ?string { return $this->ruta; }
    public function setRuta(?string $value): self { $this->ruta = $value; return $this; }
    public function getNombreOriginal(): ?string { return $this->nombreOriginal; }
    public function setNombreOriginal(?string $value): self { $this->nombreOriginal = $value; return $this; }
    public function isVigente(): bool { return $this->vigente; }
    public function setVigente(bool $value): self { $this->vigente = $value; return $this; }
    public function getSubidoPor(): ?string { return $this->subidoPor; }
    public function setSubidoPor(?string $value): self { $this->subidoPor = $value; return $this; }
}
