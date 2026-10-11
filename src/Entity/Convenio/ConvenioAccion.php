<?php

namespace App\Entity\Convenio;

use App\Entity\BaseNomenclator;
use App\Entity\Institucion\Institucion;
use App\Entity\Tramite\InstitucionExtranjera;
use App\Entity\Estructura\Pais;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'convenio.tbr_convenio_accion')]
#[ORM\Entity]
class ConvenioAccion extends BaseNomenclator
{

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Convenio::class)]
    private ?Convenio $convenio;

    /**
     * @return Convenio|null
     */
    public function getConvenio(): ?Convenio
    {
        return $this->convenio;
    }

    /**
     * @param Convenio|null $convenio
     */
    public function setConvenio(?Convenio $convenio): void
    {
        $this->convenio = $convenio;
    }

}
