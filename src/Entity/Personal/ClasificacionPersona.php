<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_clasificacion_persona')]
#[ORM\Entity]
class ClasificacionPersona extends BaseNomenclator
{    
}
