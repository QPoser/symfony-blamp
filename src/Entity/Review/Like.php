<?php

namespace App\Entity\Review;

use App\Entity\Review\Review;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 * @ORM\Table(name="likes")
 */
class Like
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Review\Review", inversedBy="likes")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     */
    private $review;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;



    public function getId()
    {
        return $this->id;
    }

    public function getValue(): ?bool
    {
        return $this->value;
    }

    public function setValue(?bool $value): self
    {
        $this->value = $value;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     * @return Like
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }
}
