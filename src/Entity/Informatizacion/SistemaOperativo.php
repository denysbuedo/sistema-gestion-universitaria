<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_sistema_operativo')]
#[ORM\Entity]
class SistemaOperativo extends BaseNomenclator
{

}
