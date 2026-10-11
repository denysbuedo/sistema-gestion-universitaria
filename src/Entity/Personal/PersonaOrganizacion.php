<?php

namespace App\Entity\Personal;

use App\Entity\BaseEntity;
use App\Entity\Personal\Organizacion;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'personal.tbr_persona_organizacion')]
#[ORM\Entity]
class PersonaOrganizacion extends BaseEntity
{

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Persona::class)]
    private ?Persona $persona;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Organizacion::class)]
    private ?Organizacion $organizacion;


    /**
     * Get the value of persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set the value of persona
     *
     * @return  self
     */
    public function setPersona($persona)
    {
        $this->persona = $persona;

        return $this;
    }











    /**
     * Get the value of organizacion
     */
    public function getOrganizacion()
    {
        return $this->organizacion;
    }

    /**
     * Set the value of organizacion
     *
     * @return  self
     */
    public function setOrganizacion($organizacion)
    {
        $this->organizacion = $organizacion;

        return $this;
    }


















}