<?php

namespace App\Entity\Institucion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbn_visibilidad')]
#[ORM\Entity]
class Visibilidad extends BaseNomenclator
{

}
