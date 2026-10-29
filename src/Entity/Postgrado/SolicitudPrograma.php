<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseEntity;
use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\Attribute\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Institucion\CategoriaAcreditacion;
use App\Entity\Institucion\Institucion;
use App\Entity\Estructura\Estructura;

#[ORM\Table(name: 'postgrado.tbd_solicitud_programa')]
#[ORM\Entity]
class SolicitudPrograma extends BaseEntity
{
    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\Regex(pattern: '/^[,0-9a-zA-ZäëïöüáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ_\s]+$/', match: true, message: 'Caracteres no válidos, por favor verifique.')]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $activo = true;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Postgrado\TipoSolicitud::class)]
    private ?TipoSolicitud $tipoSolicitud;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Postgrado\TipoSolicitudClasificacion::class)]
    private ?TipoSolicitudClasificacion $tipoSolicitudClasificacion;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: EstadoPrograma::class)]
    private ?EstadoPrograma $estadoPrograma;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Estructura\Estructura::class)]
    private ?Estructura $universidad = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Estructura\Estructura::class)]
    private ?Estructura $originalDe = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: TipoPrograma::class)]
    private ?TipoPrograma $tipoPrograma;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: RamaCiencia::class)]
    private ?RamaCiencia $ramaCiencia;


    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: ModalidadPrograma::class)]
    private ?ModalidadPrograma $modalidadPrograma;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: PresencialidadPrograma::class)]
    private ?PresencialidadPrograma $presencialidadPrograma;


    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $nombreCoordinador = null;

    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $correoCoordinador = null;


    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $telefonoCoordinador = null;


    #[ORM\Column(type: 'string', nullable: true, length: 50)]
    private ?string $codigoPrograma = null;


    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Institucion\CategoriaAcreditacion::class)]
    private ?CategoriaAcreditacion $categoriaAcreditacion = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaProximaAcreditacion;


    #[Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaAprobacion;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duracionPrograma = 0;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cantidadCreditos = 0;

    #[ORM\Column(type: 'string', length: 40, options: ['default' => 'LEGADO'])]
    private string $naturalezaSolicitud = 'LEGADO';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $justificacionSolicitud = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $organismoDemandante = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?self $programaReferencia = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $porcentajeModificacion = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $cambiaDenominacion = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $cambiaObjetivos = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $numeroResolucionMinisterial = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaResolucionMinisterial = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaPresentacion = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaDecision = null;


    public function getFechaProximaAcreditacion()
    {
        return $this->fechaProximaAcreditacion;
    }

    public function setFechaProximaAcreditacion($fechaProximaAcreditacion)
    {
        $this->fechaProximaAcreditacion = $fechaProximaAcreditacion;

        return $this;
    }


    public function getCodigoPrograma()
    {
        return $this->codigoPrograma;
    }

    public function setCodigoPrograma($codigoPrograma)
    {
        $this->codigoPrograma = $codigoPrograma;

        return $this;
    }


    public function getTelefonoCoordinador()
    {
        return $this->telefonoCoordinador;
    }

    public function setTelefonoCoordinador($telefonoCoordinador)
    {
        $this->telefonoCoordinador = $telefonoCoordinador;

        return $this;
    }


    public function getCorreoCoordinador()
    {
        return $this->correoCoordinador;
    }

    public function setCorreoCoordinador($correoCoordinador)
    {
        $this->correoCoordinador = $correoCoordinador;

        return $this;
    }


    public function getNombreCoordinador()
    {
        return $this->nombreCoordinador;
    }

    public function setNombreCoordinador($nombreCoordinador)
    {
        $this->nombreCoordinador = $nombreCoordinador;

        return $this;
    }


    public function getPresencialidadPrograma()
    {
        return $this->presencialidadPrograma??null;
    }

    public function setPresencialidadPrograma($presencialidadPrograma)
    {
        $this->presencialidadPrograma = $presencialidadPrograma;

        return $this;
    }


    public function getModalidadPrograma()
    {
        return $this->modalidadPrograma;
    }

    public function setModalidadPrograma($modalidadPrograma)
    {
        $this->modalidadPrograma = $modalidadPrograma;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $docPrograma = null;


    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $resolucionPrograma = null;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $dictamenGeneral = null;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $dictamenFinal = null;

    #[ORM\Column(type: 'string', nullable: true, length: 4)]
    private ?string $annoAcreditacion = null;


    public function getAnnoAcreditacion()
    {
        return $this->annoAcreditacion;
    }

    public function setAnnoAcreditacion($annoAcreditacion)
    {
        $this->annoAcreditacion = $annoAcreditacion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDictamenGeneral(): ?string
    {
        return $this->dictamenGeneral;
    }

    /**
     * @param string|null $dictamenGeneral
     */
    public function setDictamenGeneral(?string $dictamenGeneral): void
    {
        $this->dictamenGeneral = $dictamenGeneral;
    }


    public function getResolucionPrograma()
    {
        return $this->resolucionPrograma;
    }

    public function setResolucionPrograma($resolucionPrograma)
    {
        $this->resolucionPrograma = $resolucionPrograma;

        return $this;
    }

    public function getDictamenFinal()
    {
        return $this->dictamenFinal;
    }

    public function setDictamenFinal($dictamenFinal)
    {
        $this->dictamenFinal = $dictamenFinal;

        return $this;
    }

    public function getRamaCiencia()
    {
        return $this->ramaCiencia;
    }

    public function setRamaCiencia($ramaCiencia)
    {
        $this->ramaCiencia = $ramaCiencia;

        return $this;
    }


    public function getTipoPrograma()
    {
        return $this->tipoPrograma;
    }

    public function setTipoPrograma($tipoPrograma)
    {
        $this->tipoPrograma = $tipoPrograma;

        return $this;
    }


    public function getDocPrograma()
    {
        return $this->docPrograma;
    }

    public function setDocPrograma($docPrograma)
    {
        $this->docPrograma = $docPrograma;

        return $this;
    }


    public function getEstadoPrograma()
    {
        return $this->estadoPrograma;
    }

    public function setEstadoPrograma($estadoPrograma)
    {
        $this->estadoPrograma = $estadoPrograma;

        return $this;
    }


    public function getCategoriaAcreditacion()
    {
        return $this->categoriaAcreditacion;
    }

    public function setCategoriaAcreditacion($categoriaAcreditacion)
    {
        $this->categoriaAcreditacion = $categoriaAcreditacion;

        return $this;
    }

    /**
     * @return Estructura|null
     */
    public function getUniversidad(): ?Estructura
    {
        return $this->universidad;
    }

    /**
     * @param Estructura|null $universidad
     */
    public function setUniversidad(?Estructura $universidad): void
    {
        $this->universidad = $universidad;
    }

    /**
     * @return Estructura|null
     */
    public function getOriginalDe(): ?Estructura
    {
        return $this->originalDe;
    }

    /**
     * @param Estructura|null $originalDe
     */
    public function setOriginalDe(?Estructura $originalDe): void
    {
        $this->originalDe = $originalDe;
    }



    /**
     * @return TipoSolicitud|null
     */
    public function getTipoSolicitud(): ?TipoSolicitud
    {
        return $this->tipoSolicitud;
    }

    /**
     * @param TipoSolicitud|null $tipoSolicitud
     */
    public function setTipoSolicitud(?TipoSolicitud $tipoSolicitud): void
    {
        $this->tipoSolicitud = $tipoSolicitud;
    }

    /**
     * @return TipoSolicitudClasificacion|null
     */
    public function getTipoSolicitudClasificacion(): ?TipoSolicitudClasificacion
    {
        return $this->tipoSolicitudClasificacion;
    }

    /**
     * @param TipoSolicitudClasificacion|null $tipoSolicitudClasificacion
     */
    public function setTipoSolicitudClasificacion(?TipoSolicitudClasificacion $tipoSolicitudClasificacion): void
    {
        $this->tipoSolicitudClasificacion = $tipoSolicitudClasificacion;
    }


    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
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

    /**
     * @return int|null
     */
    public function getDuracionPrograma()
    {
        return $this->duracionPrograma;
    }

    /**
     * @param int|null $duracionPrograma
     */
    public function setDuracionPrograma(?int $duracionPrograma): void
    {
        $this->duracionPrograma = $duracionPrograma;
    }


    /**
     * @return int|null
     */
    public function getCantidadCreditos(): ?int
    {
        return $this->cantidadCreditos;
    }

    /**
     * @param int|null $cantidadCreditos
     */
    public function setCantidadCreditos(?int $cantidadCreditos): void
    {
        $this->cantidadCreditos = $cantidadCreditos;
    }

    /**
     * @return mixed
     */
    public function getFechaAprobacion()
    {
        return $this->fechaAprobacion;
    }

    /**
     * @param mixed $fechaAprobacion
     */
    public function setFechaAprobacion($fechaAprobacion): void
    {
        $this->fechaAprobacion = $fechaAprobacion;
    }

    public function getNaturalezaSolicitud(): string { return $this->naturalezaSolicitud; }
    public function setNaturalezaSolicitud(string $value): self { $this->naturalezaSolicitud = $value; return $this; }
    public function getJustificacionSolicitud(): ?string { return $this->justificacionSolicitud; }
    public function setJustificacionSolicitud(?string $value): self { $this->justificacionSolicitud = $value; return $this; }
    public function getOrganismoDemandante(): ?string { return $this->organismoDemandante; }
    public function setOrganismoDemandante(?string $value): self { $this->organismoDemandante = $value; return $this; }
    public function getProgramaReferencia(): ?self { return $this->programaReferencia; }
    public function setProgramaReferencia(?self $value): self { $this->programaReferencia = $value; return $this; }
    public function getPorcentajeModificacion(): ?int { return $this->porcentajeModificacion; }
    public function setPorcentajeModificacion(?int $value): self { $this->porcentajeModificacion = $value; return $this; }
    public function isCambiaDenominacion(): bool { return $this->cambiaDenominacion; }
    public function setCambiaDenominacion(bool $value): self { $this->cambiaDenominacion = $value; return $this; }
    public function isCambiaObjetivos(): bool { return $this->cambiaObjetivos; }
    public function setCambiaObjetivos(bool $value): self { $this->cambiaObjetivos = $value; return $this; }
    public function getNumeroResolucionMinisterial(): ?string { return $this->numeroResolucionMinisterial; }
    public function setNumeroResolucionMinisterial(?string $value): self { $this->numeroResolucionMinisterial = $value; return $this; }
    public function getFechaResolucionMinisterial(): ?\DateTimeInterface { return $this->fechaResolucionMinisterial; }
    public function setFechaResolucionMinisterial(?\DateTimeInterface $value): self { $this->fechaResolucionMinisterial = $value; return $this; }
    public function getFechaPresentacion(): ?\DateTimeInterface { return $this->fechaPresentacion; }
    public function setFechaPresentacion(?\DateTimeInterface $value): self { $this->fechaPresentacion = $value; return $this; }
    public function getFechaDecision(): ?\DateTimeInterface { return $this->fechaDecision; }
    public function setFechaDecision(?\DateTimeInterface $value): self { $this->fechaDecision = $value; return $this; }


}
