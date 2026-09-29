<?php

namespace App\Entity\Tramite;

use App\Entity\BaseEntity;
use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="tramite.tbd_ficha_salida_historial_documento")
 */
class FichaSalidaHistorialDocumento extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tramite\FichaSalida")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?FichaSalida $fichaSalida;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $archivo;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTimeInterface $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
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
