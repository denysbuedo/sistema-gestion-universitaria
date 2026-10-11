<?php

namespace App\Entity\RRHH;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'rrhh.tbn_categoria_docente')]
#[ORM\Entity]
class CategoriaDocente  extends BaseNomenclator
{   
}
