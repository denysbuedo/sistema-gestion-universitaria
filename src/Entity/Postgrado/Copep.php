<?php

namespace App\Entity\Postgrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'postgrado.tbd_copep')]
#[ORM\Entity]
class Copep extends BaseNomenclator
{

}
