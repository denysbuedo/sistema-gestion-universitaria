<?php

namespace App\Entity\Institucion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbn_rol_redes')]
#[ORM\Entity]
class RolRedesNacional extends BaseNomenclator
{

}
