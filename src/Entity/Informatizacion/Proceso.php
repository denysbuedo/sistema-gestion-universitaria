<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_proceso')]
#[ORM\Entity]
class Proceso extends BaseNomenclator
{

}
