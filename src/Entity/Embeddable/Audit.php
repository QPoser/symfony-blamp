<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 * @package App\Entity\Embeddable
 */
class Audit
{
    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedOn;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->createdOn = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        $this->updatedOn = new \DateTime();
    }
}