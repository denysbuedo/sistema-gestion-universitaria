<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbn_organismo_formador')]
#[ORM\Entity]
class OrganismoFormador extends BaseNomenclator
{

}
