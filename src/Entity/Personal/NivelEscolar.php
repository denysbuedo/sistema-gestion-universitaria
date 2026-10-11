<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_nivel_escolar')]
#[ORM\Entity]
class NivelEscolar extends BaseNomenclator
{   
}
