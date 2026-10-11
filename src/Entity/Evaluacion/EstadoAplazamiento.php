<?php

namespace App\Entity\Evaluacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evaluacion.tbn_estado_aplazamiento')]
#[ORM\Entity]
class EstadoAplazamiento extends BaseNomenclator
{

}
