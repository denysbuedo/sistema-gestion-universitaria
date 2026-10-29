<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbd_solicitud_programa_historial')]
#[ORM\Entity]
class SolicitudProgramaHistorial extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: SolicitudPrograma::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SolicitudPrograma $solicitudPrograma = null;

    #[ORM\Column(length: 50)]
    private ?string $accion = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $estadoAnterior = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $estadoNuevo = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacion = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $realizadoPor = null;

    public function getSolicitudPrograma(): ?SolicitudPrograma { return $this->solicitudPrograma; }
    public function setSolicitudPrograma(?SolicitudPrograma $value): self { $this->solicitudPrograma = $value; return $this; }
    public function getAccion(): ?string { return $this->accion; }
    public function setAccion(?string $value): self { $this->accion = $value; return $this; }
    public function getEstadoAnterior(): ?string { return $this->estadoAnterior; }
    public function setEstadoAnterior(?string $value): self { $this->estadoAnterior = $value; return $this; }
    public function getEstadoNuevo(): ?string { return $this->estadoNuevo; }
    public function setEstadoNuevo(?string $value): self { $this->estadoNuevo = $value; return $this; }
    public function getObservacion(): ?string { return $this->observacion; }
    public function setObservacion(?string $value): self { $this->observacion = $value; return $this; }
    public function getRealizadoPor(): ?string { return $this->realizadoPor; }
    public function setRealizadoPor(?string $value): self { $this->realizadoPor = $value; return $this; }
}
