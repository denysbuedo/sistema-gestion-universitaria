<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbn_estado_programa')]
#[ORM\Entity]
class EstadoPrograma extends BaseNomenclator
{

}
