<?php

namespace App\Entity\Review;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Review\ReviewCommentRepository")
 */
class ReviewComment
{
    const STATUS_ACTIVE = 'active';
    const STATUS_WAIT = 'wait';
    const STATUS_REJECTED = 'rejected';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Review", inversedBy="comments")
     * @ORM\JoinColumn(name="rev_comments_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $review;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompany;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_comments_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\ReviewComment", mappedBy="parentComment", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $childrenComments;

    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="App\Entity\Review\ReviewComment", inversedBy="childrenComments")
     * @ORM\JoinColumn(name="parent_comment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentComment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $root;

    public function __construct()
    {
        $this->childrenComments = new ArrayCollection();
        $this->setCreatedAt();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getIsCompany(): ?bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): self
    {
        $this->isCompany = $isCompany;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getNormalStatus(): ?string
    {
        switch($this->status) {
            case self::STATUS_ACTIVE:
                return 'Активен';
                break;
            case self::STATUS_WAIT:
                return 'В ожидании';
                break;
            case self::STATUS_REJECTED:
                return 'Отклонен';
                break;
        }

        return 'Недоступно';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection|ReviewComment[]
     */
    public function getChildrenComments(): Collection
    {
        return $this->childrenComments;
    }

    public function addChildrenComment(ReviewComment $childrenComment): self
    {
        if (!$this->childrenComments->contains($childrenComment)) {
            $this->childrenComments[] = $childrenComment;
            $childrenComment->setParentComment($this);
            $childrenComment->setRoot(false);
            $childrenComment->setReview($this->getReview());
        }

        return $this;
    }

    public function removeChildrenComment(ReviewComment $childrenComment): self
    {
        if ($this->childrenComments->contains($childrenComment)) {
            $this->childrenComments->removeElement($childrenComment);
            // set the owning side to null (unless already changed)
            if ($childrenComment->getParentComment() === $this) {
                $childrenComment->setParentComment(null);
            }
        }

        return $this;
    }

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): self
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getRoot(): ?bool
    {
        return $this->root;
    }

    public function setRoot(bool $root): self
    {
        $this->root = $root;

        return $this;
    }
}
