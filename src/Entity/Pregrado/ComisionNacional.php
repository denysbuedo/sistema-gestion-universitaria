<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbd_comision_nacional')]
#[ORM\Entity]
class ComisionNacional extends BaseNomenclator
{

}
