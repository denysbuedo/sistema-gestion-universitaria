<?php

namespace App\Entity\Personal;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbn_tipo_organizacion')]
#[ORM\Entity]
class TipoOrganizacion extends BaseNomenclator
{   
}
