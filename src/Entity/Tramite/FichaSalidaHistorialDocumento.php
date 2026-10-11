<?php

namespace App\Entity\Tramite;

use App\Entity\BaseEntity;
use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\Attribute\Timestampable;

#[ORM\Table(name: 'tramite.tbd_ficha_salida_historial_documento')]
#[ORM\Entity]
class FichaSalidaHistorialDocumento extends BaseEntity
{
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Tramite\FichaSalida::class)]
    private ?FichaSalida $fichaSalida;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $archivo;

    #[ORM\Column(type: 'datetime')]
    #[Timestampable(on: 'create')]
    private ?\DateTimeInterface $fecha;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Security\User::class)]
    private ?User $usuario;

    /**
     * @return FichaSalida|null
     */
    public function getFichaSalida(): ?FichaSalida
    {
        return $this->fichaSalida;
    }

    /**
     * @param FichaSalida|null $fichaSalida
     */
    public function setFichaSalida(?FichaSalida $fichaSalida): void
    {
        $this->fichaSalida = $fichaSalida;
    }

    /**
     * @return string|null
     */
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    /**
     * @param string|null $nombre
     */
    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string|null
     */
    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    /**
     * @param string|null $archivo
     */
    public function setArchivo(?string $archivo): void
    {
        $this->archivo = $archivo;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    /**
     * @param \DateTimeInterface|null $fecha
     */
    public function setFecha(?\DateTimeInterface $fecha): void
    {
        $this->fecha = $fecha;
    }

    /**
     * @return User|null
     */
    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    /**
     * @param User|null $usuario
     */
    public function setUsuario(?User $usuario): void
    {
        $this->usuario = $usuario;
    }
}
