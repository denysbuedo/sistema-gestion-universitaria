<?php

namespace App\Entity\Informatizacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'informatizacion.tbn_tipo_sistema')]
#[ORM\Entity]
class TipoSistema extends BaseNomenclator
{

}
