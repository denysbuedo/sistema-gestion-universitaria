<?php

namespace App\Entity\Pregrado;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pregrado.tbn_tipo_programa_academico')]
#[ORM\Entity]
class TipoProgramaAcademico extends BaseNomenclator
{

}
