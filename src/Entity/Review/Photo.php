<?php

namespace App\Entity\Review;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Review\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Image(
     * )
     *     minWidth= 200,
     *     maxWidth= 200,
     *     minHeight= 200,
     *     maxHeight= 400
     */
    private $photo;

    public function getId()
    {
        return $this->id;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
