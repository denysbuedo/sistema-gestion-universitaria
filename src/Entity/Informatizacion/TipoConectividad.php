<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_tipo_conectividad')]
#[ORM\Entity]
class TipoConectividad extends BaseNomenclator
{

}
