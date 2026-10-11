<?php

namespace App\Entity\Tramite;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tramite.tbd_plan_mision')]
#[ORM\Entity]
class PlanMision extends BaseNomenclator
{

}
