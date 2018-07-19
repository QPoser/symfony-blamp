<?php

namespace App\Entity\Company;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Review;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
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
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\Regex(
     *     pattern = "^((\+7|7|8)+([0-9]){10})^",
     *     message = "The phone '{{ value }}' is not a valid phone. Valid phone is - +79876543210."
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time()
     */
    private $start_work;

    /**
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time()
     */
    private $end_work;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Url(
     *    protocols={"http", "https"},
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Image(
     *     minWidth= 200,
     *     maxWidth= 400,
     *     minHeight= 200,
     *     maxHeight= 400
     * )
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     min = 5,
     *     max = 255,
     * )
     */
    private $reject_reason;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="company")
     */
    private $reviews;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $assessment;


    public function calcAssessment()
    {
        $assessment = null;
        foreach ($this->reviews as $review) {
            $assessment += $review->getAssessment();
        }
        $this->assessment = $assessment / count($this->reviews);
    }



    // Status

    public function setActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function setWaite()
    {
        $this->status = self::STATUS_WAIT;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }


    // Getters and setters

    public function getId()
    {
        return $this->id;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStartWork(): ?\DateTimeInterface
    {
        return $this->start_work;
    }

    public function setStartWork(?\DateTimeInterface $start_work): self
    {
        $this->start_work = $start_work;

        return $this;
    }

    public function getEndWork(): ?\DateTimeInterface
    {
        return $this->end_work;
    }

    public function setEndWork(?\DateTimeInterface $end_work): self
    {
        $this->end_work = $end_work;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): self
    {
        $this->site = $site;

        return $this;
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

    public function getNewPhoto(): self
    {
        return $this;
    }


    private $new_photo;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }
    public function setNewPhoto(?string $photo): self
    {
        $this->new_photo = $photo;

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
        return $this->reject_reason;
    }

    public function setRejectReason(?string $reject_reason): self
    {
        $this->reject_reason = $reject_reason;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setCompany($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getCompany() === $this) {
                $review->setCompany(null);
            }
        }

        return $this;
    }

    public function getAssessment(): ?float
    {
        return $this->assessment;
    }

    public function setAssessment(float $assessment): self
    {
        $this->assessment = $assessment;

        return $this;
    }
}
