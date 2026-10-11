<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseEntity;

use App\Entity\Estructura\Estructura;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Informatizacion\Marca;
use App\Entity\Informatizacion\Modelo;
use App\Entity\Informatizacion\SistemaOperativo;


#[ORM\Table(name: 'informatizacion.tbd_telefono_celular')]
#[ORM\Entity]
class TelefonoCelular extends BaseEntity
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Informatizacion\Marca::class)]
    private ?Marca $marca;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Informatizacion\Modelo::class)]
    private ?Modelo $modelo;


    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Informatizacion\SistemaOperativo::class)]
    private ?SistemaOperativo $sistemaOperativo;
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Estructura\Estructura::class)]
    private ?Estructura $estructura;

    /**
     * @return \App\Entity\Informatizacion\Marca|null
     */
    public function getMarca(): ?\App\Entity\Informatizacion\Marca
    {
        return $this->marca;
    }

    /**
     * @param \App\Entity\Informatizacion\Marca|null $marca
     */
    public function setMarca(?\App\Entity\Informatizacion\Marca $marca): void
    {
        $this->marca = $marca;
    }

    /**
     * @return \App\Entity\Informatizacion\Modelo|null
     */
    public function getModelo(): ?\App\Entity\Informatizacion\Modelo
    {
        return $this->modelo;
    }

    /**
     * @param \App\Entity\Informatizacion\Modelo|null $modelo
     */
    public function setModelo(?\App\Entity\Informatizacion\Modelo $modelo): void
    {
        $this->modelo = $modelo;
    }

    /**
     * @return \App\Entity\Informatizacion\SistemaOperativo|null
     */
    public function getSistemaOperativo(): ?\App\Entity\Informatizacion\SistemaOperativo
    {
        return $this->sistemaOperativo;
    }

    /**
     * @param \App\Entity\Informatizacion\SistemaOperativo|null $sistemaOperativo
     */
    public function setSistemaOperativo(?\App\Entity\Informatizacion\SistemaOperativo $sistemaOperativo): void
    {
        $this->sistemaOperativo = $sistemaOperativo;
    }

    /**
     * @return Estructura|null
     */
    public function getEstructura(): ?Estructura
    {
        return $this->estructura;
    }

    /**
     * @param Estructura|null $estructura
     */
    public function setEstructura(?Estructura $estructura): void
    {
        $this->estructura = $estructura;
    }


}
