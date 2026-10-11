<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbn_documento')]
#[ORM\Entity]
class Documento extends BaseNomenclator
{

}
