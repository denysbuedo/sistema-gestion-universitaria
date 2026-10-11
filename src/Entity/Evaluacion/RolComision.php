<?php

namespace App\Entity\Evaluacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evaluacion.tbn_rol_comision')]
#[ORM\Entity]
class RolComision extends BaseNomenclator
{

}
