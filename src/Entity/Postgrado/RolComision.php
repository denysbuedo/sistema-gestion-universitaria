<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbn_rol_comision')]
#[ORM\Entity]
class RolComision extends BaseNomenclator
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $copep = true;

    public function getCopep()
    {
        return $this->copep;
    }

    public function setCopep($copep)
    {
        $this->copep = $copep;

        return $this;
    }
}
