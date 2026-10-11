<?php

namespace App\Entity\Institucion;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'institucion.tbr_institucion_redes_sociales')]
#[ORM\Entity]
class InstitucionRedesSociales extends BaseEntity
{

    #[ORM\Column(type: 'string', nullable: false, length: 255)]
    private ?string $perfil = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Institucion::class)]
    private ?Institucion $institucion;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: RedSocial::class)]
    private ?RedSocial $redSocial;

    /**
     * @return string|null
     */
    public function getPerfil(): ?string
    {
        return $this->perfil;
    }

    /**
     * @param string|null $perfil
     */
    public function setPerfil(?string $perfil): void
    {
        $this->perfil = $perfil;
    }

    /**
     * @return Institucion|null
     */
    public function getInstitucion(): ?Institucion
    {
        return $this->institucion;
    }

    /**
     * @param Institucion|null $institucion
     */
    public function setInstitucion(?Institucion $institucion): void
    {
        $this->institucion = $institucion;
    }

    /**
     * @return Institucion|null
     */
    public function getRedSocial(): ?RedSocial
    {
        return $this->redSocial;
    }

    /**
     * @param Institucion|null $redSocial
     */
    public function setRedSocial(?RedSocial $redSocial): void
    {
        $this->redSocial = $redSocial;
    }

}
