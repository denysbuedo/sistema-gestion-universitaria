<?php

namespace App\Entity\Estructura;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'estructura.tbn_nivel')]
#[ORM\Entity]
class Nivel extends BaseNomenclator
{
}