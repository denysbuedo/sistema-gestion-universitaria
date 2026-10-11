<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_publico_objetivo')]
#[ORM\Entity]
class PublicoObjetivo extends BaseNomenclator
{

}
