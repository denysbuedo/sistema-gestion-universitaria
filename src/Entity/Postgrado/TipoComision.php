<?php

namespace App\Entity\Postgrado;
 
use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbn_tipo_comision')]
#[ORM\Entity]
class TipoComision extends BaseNomenclator
{
    #[ORM\Column(type: 'string', nullable: true, length: 9)]
    private ?string $siglas=null;


    public function getSiglas()
    {
        return $this->siglas;
    }

    public function setSiglas($siglas)
    {
        $this->siglas = $siglas;

        return $this;
    }
}
