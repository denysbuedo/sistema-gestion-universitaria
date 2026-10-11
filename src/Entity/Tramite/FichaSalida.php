<?php

namespace App\Entity\Tramite;

use App\Entity\BaseEntity;
use App\Entity\Estructura\Pais;
use App\Entity\Personal\Persona;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Institucion\Institucion;
use App\Entity\Security\User;

#[ORM\Table(name: 'tramite.tbd_ficha_salida')]
#[ORM\Entity]
class FichaSalida extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Personal\Persona::class)]
    private ?Persona $persona = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Institucion\Institucion::class)]
    private ?Institucion $institucionCubana = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Estructura\Pais::class)]
    private ?Pais $pais = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Tramite\InstitucionExtranjera::class)]
    private ?InstitucionExtranjera $institucionExtranjera = null;


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $provinciaExtranjera = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Tramite\ConceptoSalida::class)]
    private ?ConceptoSalida $conceptoSalida = null;


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $objetivo = null;


    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaSalidaPrevista;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaRegresoPrevista;


    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaSalidaReal;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaRegresoReal;


    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tiempoEstancia;


    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Tramite\TipoPasaporte::class)]
    private ?TipoPasaporte $tipoPasaporte = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $numeroPasaporte=null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaEmisionPasaporte;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaCaducidadPasaporte;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $requiereVisa = false;
    #[ORM\Column(type: 'boolean', nullable: true)]
    private $aprobadoFactoresIes = false;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Tramite\EstadoFichaSalida::class)]
    private ?EstadoFichaSalida $estadoFichaSalida;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $cartaInvitacion = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fechaFirmaDirectivo;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    private $directivoFirma;


    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private ?string $fichaSalida = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mandatosMision = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $clasificacion = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $consultaEmbajada = null;

    /**
     * @return Persona|null
     */
    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    /**
     * @param Persona|null $persona
     */
    public function setPersona(?Persona $persona): void
    {
        $this->persona = $persona;
    }

    /**
     * @return Institucion|null
     */
    public function getInstitucionCubana(): ?Institucion
    {
        return $this->institucionCubana;
    }

    /**
     * @param Institucion|null $institucionCubana
     */
    public function setInstitucionCubana(?Institucion $institucionCubana): void
    {
        $this->institucionCubana = $institucionCubana;
    }

    /**
     * @return Pais|null
     */
    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    /**
     * @param Pais|null $pais
     */
    public function setPais(?Pais $pais): void
    {
        $this->pais = $pais;
    }

    /**
     * @return InstitucionExtranjera|null
     */
    public function getInstitucionExtranjera(): ?InstitucionExtranjera
    {
        return $this->institucionExtranjera;
    }

    /**
     * @param InstitucionExtranjera|null $institucionExtranjera
     */
    public function setInstitucionExtranjera(?InstitucionExtranjera $institucionExtranjera): void
    {
        $this->institucionExtranjera = $institucionExtranjera;
    }

    /**
     * @return string|null
     */
    public function getProvinciaExtranjera(): ?string
    {
        return $this->provinciaExtranjera;
    }

    /**
     * @param string|null $provinciaExtranjera
     */
    public function setProvinciaExtranjera(?string $provinciaExtranjera): void
    {
        $this->provinciaExtranjera = $provinciaExtranjera;
    }

    /**
     * @return ConceptoSalida|null
     */
    public function getConceptoSalida(): ?ConceptoSalida
    {
        return $this->conceptoSalida;
    }

    /**
     * @param ConceptoSalida|null $conceptoSalida
     */
    public function setConceptoSalida(?ConceptoSalida $conceptoSalida): void
    {
        $this->conceptoSalida = $conceptoSalida;
    }

    /**
     * @return string|null
     */
    public function getObjetivo(): ?string
    {
        return $this->objetivo;
    }

    /**
     * @param string|null $objetivo
     */
    public function setObjetivo(?string $objetivo): void
    {
        $this->objetivo = $objetivo;
    }

    /**
     * @return mixed
     */
    public function getFechaSalidaPrevista()
    {
        return $this->fechaSalidaPrevista;
    }

    /**
     * @param mixed $fechaSalidaPrevista
     */
    public function setFechaSalidaPrevista($fechaSalidaPrevista): void
    {
        $this->fechaSalidaPrevista = $fechaSalidaPrevista;
    }

    /**
     * @return mixed
     */
    public function getFechaRegresoPrevista()
    {
        return $this->fechaRegresoPrevista;
    }

    /**
     * @param mixed $fechaRegresoPrevista
     */
    public function setFechaRegresoPrevista($fechaRegresoPrevista): void
    {
        $this->fechaRegresoPrevista = $fechaRegresoPrevista;
    }

    /**
     * @return mixed
     */
    public function getFechaSalidaReal()
    {
        return $this->fechaSalidaReal;
    }

    /**
     * @param mixed $fechaSalidaReal
     */
    public function setFechaSalidaReal($fechaSalidaReal): void
    {
        $this->fechaSalidaReal = $fechaSalidaReal;
    }

    /**
     * @return mixed
     */
    public function getFechaRegresoReal()
    {
        return $this->fechaRegresoReal;
    }

    /**
     * @param mixed $fechaRegresoReal
     */
    public function setFechaRegresoReal($fechaRegresoReal): void
    {
        $this->fechaRegresoReal = $fechaRegresoReal;
    }

    /**
     * @return int|null
     */
    public function getTiempoEstancia(): ?int
    {
        return $this->tiempoEstancia;
    }

    /**
     * @param int|null $tiempoEstancia
     */
    public function setTiempoEstancia(?int $tiempoEstancia): void
    {
        $this->tiempoEstancia = $tiempoEstancia;
    }

    /**
     * @return TipoPasaporte|null
     */
    public function getTipoPasaporte(): ?TipoPasaporte
    {
        return $this->tipoPasaporte;
    }

    /**
     * @param TipoPasaporte|null $tipoPasaporte
     */
    public function setTipoPasaporte(?TipoPasaporte $tipoPasaporte): void
    {
        $this->tipoPasaporte = $tipoPasaporte;
    }

    /**
     * @return string|null
     */
    public function getNumeroPasaporte(): ?string
    {
        return $this->numeroPasaporte;
    }

    /**
     * @param string|null $numeroPasaporte
     */
    public function setNumeroPasaporte(?string $numeroPasaporte): void
    {
        $this->numeroPasaporte = $numeroPasaporte;
    }

    /**
     * @return mixed
     */
    public function getFechaEmisionPasaporte()
    {
        return $this->fechaEmisionPasaporte;
    }

    /**
     * @param mixed $fechaEmisionPasaporte
     */
    public function setFechaEmisionPasaporte($fechaEmisionPasaporte): void
    {
        $this->fechaEmisionPasaporte = $fechaEmisionPasaporte;
    }

    /**
     * @return mixed
     */
    public function getFechaCaducidadPasaporte()
    {
        return $this->fechaCaducidadPasaporte;
    }

    /**
     * @param mixed $fechaCaducidadPasaporte
     */
    public function setFechaCaducidadPasaporte($fechaCaducidadPasaporte): void
    {
        $this->fechaCaducidadPasaporte = $fechaCaducidadPasaporte;
    }

    /**
     * @return bool
     */
    public function isRequiereVisa(): bool
    {
        return $this->requiereVisa;
    }

    /**
     * @param bool $requiereVisa
     */
    public function setRequiereVisa(bool $requiereVisa): void
    {
        $this->requiereVisa = $requiereVisa;
    }

    /**
     * @return bool
     */
    public function isAprobadoFactoresIes(): bool
    {
        return $this->aprobadoFactoresIes;
    }

    /**
     * @param bool $aprobadoFactoresIes
     */
    public function setAprobadoFactoresIes(bool $aprobadoFactoresIes): void
    {
        $this->aprobadoFactoresIes = $aprobadoFactoresIes;
    }

    /**
     * @return EstadoFichaSalida|null
     */
    public function getEstadoFichaSalida(): ?EstadoFichaSalida
    {
        return $this->estadoFichaSalida;
    }

    /**
     * @param EstadoFichaSalida|null $estadoFichaSalida
     */
    public function setEstadoFichaSalida(?EstadoFichaSalida $estadoFichaSalida): void
    {
        $this->estadoFichaSalida = $estadoFichaSalida;
    }

    /**
     * @return string|null
     */
    public function getCartaInvitacion(): ?string
    {
        return $this->cartaInvitacion;
    }

    /**
     * @param string|null $cartaInvitacion
     */
    public function setCartaInvitacion(?string $cartaInvitacion): void
    {
        $this->cartaInvitacion = $cartaInvitacion;
    }

    /**
     * @return mixed
     */
    public function getFechaFirmaDirectivo()
    {
        return $this->fechaFirmaDirectivo;
    }

    /**
     * @param mixed $fechaFirmaDirectivo
     */
    public function setFechaFirmaDirectivo($fechaFirmaDirectivo): void
    {
        $this->fechaFirmaDirectivo = $fechaFirmaDirectivo;
    }

    /**
     * @return mixed
     */
    public function getDirectivoFirma()
    {
        return $this->directivoFirma;
    }

    /**
     * @param mixed $directivoFirma
     */
    public function setDirectivoFirma($directivoFirma): void
    {
        $this->directivoFirma = $directivoFirma;
    }

    public function getFichaSalida(): ?string
    {
        return $this->fichaSalida;
    }

    public function setFichaSalida(?string $fichaSalida): void
    {
        $this->fichaSalida = $fichaSalida;
    }

    /**
     * @return string|null
     */
    public function getMandatosMision(): ?string
    {
        return $this->mandatosMision;
    }

    /**
     * @param string|null $mandatosMision
     */
    public function setMandatosMision(?string $mandatosMision): void
    {
        $this->mandatosMision = $mandatosMision;
    }

    public function getClasificacion(): ?string
    {
        return $this->clasificacion;
    }

    public function setClasificacion(?string $clasificacion): void
    {
        $this->clasificacion = $clasificacion;
    }

    public function getConsultaEmbajada(): ?bool
    {
        return $this->consultaEmbajada;
    }

    public function setConsultaEmbajada(?bool $consultaEmbajada): void
    {
        $this->consultaEmbajada = $consultaEmbajada;
    }

}
