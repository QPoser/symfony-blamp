<?php

namespace App\Entity\Review;

use App\Entity\Company\Company;
use App\Entity\Review\Like;
use App\Entity\Review\ReviewComment;
use App\Entity\Review\ReviewPhoto;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Review
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
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\ReviewPhoto", mappedBy="review", orphanRemoval=true)
     */
    private $photos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\Company", inversedBy="reviews")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\Column(type="integer")
     */
    private $assessment;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rejectReason;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\ReviewComment", mappedBy="review", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review\Like", mappedBy="review", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likeCounter;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislikeCounter;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad()
     */
    public function updateAssessment() {
        $company = $this->getCompany();
        $company->calcAssessment();
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAssessment(): ?int
    {
        return $this->assessment;
    }

    public function setAssessment(int $assessment): self
    {
        $this->assessment = $assessment;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }

    public function setRejectReason(?string $rejectReason): self
    {
        $this->rejectReason = $rejectReason;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(ReviewComment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setReview($this);
        }

        return $this;
    }

    public function removeComment(ReviewComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getReview() === $this) {
                $comment->setReview(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setReview($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getReview() === $this) {
                $like->setReview(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReviewPhoto[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(ReviewPhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setReview($this);
        }

        return $this;
    }

    public function removePhoto(ReviewPhoto $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getReview() === $this) {
                $photo->setReview(null);
            }
        }

        return $this;
    }

    public function likesCount() {
        $likeCounter = null;
        $dislikeCounter = null;
        foreach ($this->likes as $like) {
            if ($like->getValue() == 1) {
                $likeCounter++;
            }
            if ($like->getValue() == 0) {
                $dislikeCounter++;
            }
        }
        $this->likeCounter = $likeCounter;
        $this->dislikeCounter = $dislikeCounter;
    }

    public function getLikeCounter(): ?int
    {
        return $this->likeCounter;
    }

    public function setLikeCounter(?int $likeCounter): self
    {
        $this->likeCounter = $likeCounter;

        return $this;
    }

    public function getDislikeCounter(): ?int
    {
        return $this->dislikeCounter;
    }

    public function setDislikeCounter(?int $dislikeCounter): self
    {
        $this->dislikeCounter = $dislikeCounter;

        return $this;
    }
}
