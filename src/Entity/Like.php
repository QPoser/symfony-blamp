<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 * @ORM\Table(name="likes")
 * @ORM\HasLifecycleCallbacks()
 */
class Like
{
    const LIKE = true;
    const DISLIKE = false;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="likes")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     */
    private $review;

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

    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(Review $review): self
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

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     * @ORM\PostRemove()
     */
    public function updateLikes() {
        /**
         * @var $review Review
         */
        $review = $this->review;
        $review->likesCount();
    }
}
