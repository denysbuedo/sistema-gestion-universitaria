<?php

namespace App\Entity\Traza;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'traza.tbn_tipo_traza')]
#[ORM\Entity]
class TipoTraza extends BaseNomenclator
{    
}
