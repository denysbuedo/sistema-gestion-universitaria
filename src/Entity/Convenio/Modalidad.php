<?php

namespace App\Entity\Convenio;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'convenio.tbn_modalida')]
#[ORM\Entity]
class Modalidad extends BaseNomenclator
{

}
