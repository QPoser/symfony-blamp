<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(type="string")
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 400,
     *     minHeight = 200,
     *     maxHeight = 400,
     *
     * )
     */
    private $photo;

    /**
     * @ORM\Column(name="uploaded_on", type="datetime")
     */
    private $uploadedOn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="photos")
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

    /**
     * @ORM\PrePersist()
     * @return ReviewPhoto
     */
    public function setUploadedOn(): self
    {
        $this->uploadedOn = new \DateTime();

        return $this;
    }
}
