<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbd_comision')]
#[ORM\Entity]
class Comision extends BaseNomenclator
{

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Postgrado\TipoComision::class)]
    private ?TipoComision $tipoComision;

    /**
     * @return TipoComision|null
     */
    public function getTipoComision(): ?TipoComision
    {
        return $this->tipoComision;
    }

    /**
     * @param TipoComision|null $tipoComision
     */
    public function setTipoComision(?TipoComision $tipoComision): void
    {
        $this->tipoComision = $tipoComision;
    }



}
