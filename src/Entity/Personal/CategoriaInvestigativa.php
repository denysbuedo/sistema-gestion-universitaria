<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_categoria_investigativa')]
#[ORM\Entity]
class CategoriaInvestigativa extends BaseNomenclator
{   
}
