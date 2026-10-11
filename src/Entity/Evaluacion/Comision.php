<?php

namespace App\Entity\Evaluacion;

use App\Entity\BaseNomenclator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evaluacion.tbd_comision')]
#[ORM\Entity]
class Comision extends BaseNomenclator
{
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Solicitud::class)]
    private ?Solicitud $solicitud;

    /**
     * @return Solicitud|null
     */
    public function getSolicitud(): ?Solicitud
    {
        return $this->solicitud;
    }

    /**
     * @param Solicitud|null $solicitud
     */
    public function setSolicitud(?Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud;
    }


}
