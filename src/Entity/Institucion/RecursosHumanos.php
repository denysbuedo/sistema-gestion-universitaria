<?php

namespace App\Entity\Institucion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbn_recursos_humanos')]
#[ORM\Entity]
class RecursosHumanos extends BaseNomenclator
{

}

