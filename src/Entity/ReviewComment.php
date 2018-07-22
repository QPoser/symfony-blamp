<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewCommentRepository")
 * @Gedmo\Tree(type="nested")
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
     * @ORM\ManyToOne(targetEntity="Review", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="rev_comments_id", referencedColumnName="id", nullable=false)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="user_comments_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

//
//* @ORM\ManyToOne(targetEntity="App\Entity\ReviewComment")
//* @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
//
    /**
     * @Gedmo\TreeRoot()
     * @ORM\Column(type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="App\Entity\ReviewComment", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReviewComment", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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
///////////////////////////////////////////////////
    public function getRoot()
    {
        return $this->root;
    }
    public function getLevel()
    {
        return $this->lvl;
    }
    public function getChildren()
    {
        return $this->children;
    }
    public function getLeft()
    {
        return $this->lft;
    }
    public function getRight()
    {
        return $this->rgt;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(ReviewComment $parent = null)
    {
        $this->parent = $parent;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;//->format('Y-m-d');
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;//->format('Y-m-d');
    }
}
