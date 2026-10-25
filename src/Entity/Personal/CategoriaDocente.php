<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_categoria_docente')]
#[ORM\Entity]
class CategoriaDocente  extends BaseNomenclator
{
    #[ORM\Column(type: 'string', length: 20, nullable: true, unique: true)]
    private ?string $codigo = null;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $clasificacion = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $requiereAprobacionMes = false;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $autoridadOtorgamiento = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duracionMaximaAnnos = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $orden = 0;

    public function getCodigo(): ?string { return $this->codigo; }
    public function setCodigo(?string $codigo): self { $this->codigo = $codigo; return $this; }
    public function getClasificacion(): ?string { return $this->clasificacion; }
    public function setClasificacion(?string $clasificacion): self { $this->clasificacion = $clasificacion; return $this; }
    public function isRequiereAprobacionMes(): bool { return $this->requiereAprobacionMes; }
    public function setRequiereAprobacionMes(bool $valor): self { $this->requiereAprobacionMes = $valor; return $this; }
    public function getAutoridadOtorgamiento(): ?string { return $this->autoridadOtorgamiento; }
    public function setAutoridadOtorgamiento(?string $autoridad): self { $this->autoridadOtorgamiento = $autoridad; return $this; }
    public function getDuracionMaximaAnnos(): ?int { return $this->duracionMaximaAnnos; }
    public function setDuracionMaximaAnnos(?int $annos): self { $this->duracionMaximaAnnos = $annos; return $this; }
    public function getOrden(): int { return $this->orden; }
    public function setOrden(int $orden): self { $this->orden = $orden; return $this; }
}
