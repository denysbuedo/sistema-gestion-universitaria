<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_visibilidad')]
#[ORM\Entity]
class Visibilidad extends BaseNomenclator
{

}
