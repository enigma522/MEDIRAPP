<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

trait Timestamp
{
    private $createdAt;

    public function getCreatedAt(): mixed{
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function prePersist(){
        $this->createdAt = new \DateTime();
    }

}
