<?php

namespace App\Entity\Economia;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'convenio.tbn_concepto_gasto')]
#[ORM\Entity]
class ConceptoGasto extends BaseNomenclator
{

}
