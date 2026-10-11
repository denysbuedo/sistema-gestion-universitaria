<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbn_estado_programa_academico')]
#[ORM\Entity]
class EstadoProgramaAcademico extends BaseNomenclator
{

}
