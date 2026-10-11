<?php

namespace App\Entity\Tramite;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tramite.tbn_tramite')]
#[ORM\Entity]
class Tramite extends BaseNomenclator
{

}
