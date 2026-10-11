<?php

namespace App\Entity\Tramite;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tramite.tbn_concepto_salida')]
#[ORM\Entity]
class ConceptoSalida extends BaseNomenclator
{

}
