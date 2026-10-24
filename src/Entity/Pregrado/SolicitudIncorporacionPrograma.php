<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseEntity;
use App\Entity\Institucion\Institucion;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'pregrado.tbd_solicitud_incorporacion_programa')]
#[ORM\Entity]
class SolicitudIncorporacionPrograma extends BaseEntity
{
    public const PENDIENTE = 'PENDIENTE';
    public const APROBADA = 'APROBADA';
    public const RECHAZADA = 'RECHAZADA';

    #[ORM\ManyToOne(targetEntity: SolicitudProgramaAcademico::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SolicitudProgramaAcademico $programa = null;

    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Institucion $institucion = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $fundamentacion = null;

    #[ORM\Column(type: 'boolean')]
    private bool $modalidadDiurno = false;

    #[ORM\Column(type: 'boolean')]
    private bool $modalidadPorEncuentro = false;

    #[ORM\Column(type: 'boolean')]
    private bool $modalidadADistancia = false;

    #[ORM\Column(type: 'string', length: 20)]
    private string $estado = self::PENDIENTE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observaciones = null;

    public function getPrograma(): ?SolicitudProgramaAcademico { return $this->programa; }
    public function setPrograma(?SolicitudProgramaAcademico $programa): self { $this->programa = $programa; return $this; }
    public function getInstitucion(): ?Institucion { return $this->institucion; }
    public function setInstitucion(?Institucion $institucion): self { $this->institucion = $institucion; return $this; }
    public function getFundamentacion(): ?string { return $this->fundamentacion; }
    public function setFundamentacion(?string $fundamentacion): self { $this->fundamentacion = $fundamentacion; return $this; }
    public function getModalidadDiurno(): bool { return $this->modalidadDiurno; }
    public function setModalidadDiurno(bool $valor): self { $this->modalidadDiurno = $valor; return $this; }
    public function getModalidadPorEncuentro(): bool { return $this->modalidadPorEncuentro; }
    public function setModalidadPorEncuentro(bool $valor): self { $this->modalidadPorEncuentro = $valor; return $this; }
    public function getModalidadADistancia(): bool { return $this->modalidadADistancia; }
    public function setModalidadADistancia(bool $valor): self { $this->modalidadADistancia = $valor; return $this; }
    public function getEstado(): string { return $this->estado; }
    public function setEstado(string $estado): self { $this->estado = $estado; return $this; }
    public function getObservaciones(): ?string { return $this->observaciones; }
    public function setObservaciones(?string $observaciones): self { $this->observaciones = $observaciones; return $this; }

    #[Assert\Callback]
    public function validarModalidad(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if (!$this->modalidadDiurno && !$this->modalidadPorEncuentro && !$this->modalidadADistancia) {
            $context->buildViolation('Seleccione al menos una modalidad.')->atPath('modalidadDiurno')->addViolation();
        }
    }
}
