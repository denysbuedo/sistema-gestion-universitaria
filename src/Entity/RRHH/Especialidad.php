<?php

namespace App\Entity\RRHH;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'rrhh.tbn_especialidad')]
#[ORM\Entity]
class Especialidad  extends BaseNomenclator
{   
}
