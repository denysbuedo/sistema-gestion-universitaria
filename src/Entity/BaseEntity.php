<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\Attribute\Timestampable;

#[ORM\MappedSuperclass]
abstract class BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    protected $creado;

    #[Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    protected $actualizado;
  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreado()
    {
        return $this->creado;
    }
    
    public function getActualizado()
    {
        return $this->actualizado;
    }
}
