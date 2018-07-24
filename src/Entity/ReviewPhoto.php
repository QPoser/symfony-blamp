<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewPhotoRepository")
 */
class ReviewPhoto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="uploaded_on", type="datetime")
     */
    private $uploadedOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="photos", cascade={"persist"})
     * @ORM\JoinColumn(name="photo_review_id", referencedColumnName="id")
     */
    private $review;



    public function __construct()
    {
        $this->uploadedOn = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getUploadedOn(): ?\DateTimeInterface
    {
        return $this->uploadedOn;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setUploadedOn(\DateTimeInterface $uploadedOn): self
    {
        $this->uploadedOn = $uploadedOn;

        return $this;
    }
}
