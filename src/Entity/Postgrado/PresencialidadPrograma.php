<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbn_presencialidad_programa')]
#[ORM\Entity]
class PresencialidadPrograma extends BaseNomenclator
{

}
