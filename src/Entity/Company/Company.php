<?php

namespace App\Entity\Company;

use App\Entity\Category\Category;
use App\Entity\Advert\AdvertDescription;
use App\Entity\User;
use Beelab\TagBundle\Tag\TaggableInterface;
use Beelab\TagBundle\Tag\TagInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Review\Review;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\CompanyRepository")
 */
class Company implements TaggableInterface
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
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $address;

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
     * )
     *     minWidth= 200,
     *     maxWidth= 200,
     *     minHeight= 200,
     *     maxHeight= 400
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
     * @ORM\OneToMany(targetEntity="App\Entity\Review\Review", mappedBy="company", orphanRemoval=true, cascade={"persist"})
     */
    private $reviews;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $assessment;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $fixedAssessment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="favoriteCompanies")
     */
    private $usersFavor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="businessCompanies")
     */
    private $businessUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company\BusinessRequest", mappedBy="company", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $businessRequests;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category\Category", mappedBy="companies", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="companies_categories")
     * @OrderBy({"num" = "ASC"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company\Tag")
     * @OrderBy({"name" = "ASC"})
     */
    private $tags;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Advert\AdvertDescription", mappedBy="company")
     */
    private $advertDescription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company\CouponType", mappedBy="company")
     */
    private $couponTypes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company\Coupon", mappedBy="company")
     */
    private $coupons;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Email()
     */
    private $creatorEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isProtected;

    private $newPhoto;

    public function isProtected()
    {
        if ($this->isProtected) {
            return true;
        }

        return false;
    }

    public function setProtected()
    {
        $this->isProtected = true;
    }

    public function setUnprotected()
    {
        $this->isProtected = false;
    }
    private $tagsText;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


    public function calcAssessment()
    {
        $assessment = null;
        $count = 0;
        if (count($this->reviews) > 0) {
            /** @var Review $review */
            foreach ($this->reviews as $review) {
                if ($review->isActive()) {
                    $count++;
                    $assessment += $review->getAssessment();
                }
            }

            if ($count == 0) {
                $this->assessment = null;
                return true;
            }

            $this->assessment = $assessment / $count;

            return true;
        }

        $this->assessment = null;

    }



    // Status

    public function setActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function setWait()
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

    public function isWait()
    {
        return $this->status == self::STATUS_WAIT;
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

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->usersFavor = new ArrayCollection();
        $this->businessUsers = new ArrayCollection();
        $this->businessRequests = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->couponTypes = new ArrayCollection();
        $this->coupons = new ArrayCollection();
    }

    public function setNewPhoto(?string $photo): self
    {
        $this->newPhoto = $photo;

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

    public function getActiveReviews(): array
    {
        $reviews = [];
        foreach ($this->reviews as $review) {
            if ($review->isActive()) {
                $reviews[] = $review;
            }
        }
        return $reviews;
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

    /**
     * @return Collection|User[]
     */
    public function getUsersFavor(): Collection
    {
        return $this->usersFavor;
    }

    public function addUsersFavor(User $usersFavor): self
    {
        if (!$this->usersFavor->contains($usersFavor)) {
            $this->usersFavor[] = $usersFavor;
            $usersFavor->addFavoriteCompany($this);
        }

        return $this;
    }

    public function removeUsersFavor(User $usersFavor): self
    {
        if ($this->usersFavor->contains($usersFavor)) {
            $this->usersFavor->removeElement($usersFavor);
            $usersFavor->removeFavoriteCompany($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getBusinessUsers(): Collection
    {
        return $this->businessUsers;
    }

    public function addBusinessUser(User $businessUser): self
    {
        if (!$this->businessUsers->contains($businessUser)) {
            $this->businessUsers[] = $businessUser;
            $businessUser->addBusinessCompany($this);
        }

        return $this;
    }

    public function removeBusinessUser(User $businessUser): self
    {
        if ($this->businessUsers->contains($businessUser)) {
            $this->businessUsers->removeElement($businessUser);
            $businessUser->removeBusinessCompany($this);
        }

        return $this;
    }

    /**
     * @return Collection|BusinessRequest[]
     */
    public function getBusinessRequests(): Collection
    {
        return $this->businessRequests;
    }

    public function addBusinessRequest(BusinessRequest $businessRequest): self
    {
        if (!$this->businessRequests->contains($businessRequest)) {
            $this->businessRequests[] = $businessRequest;
            $businessRequest->setUser($this);
        }

        return $this;
    }

    public function removeBusinessRequest(BusinessRequest $businessRequest): self
    {
        if ($this->businessRequests->contains($businessRequest)) {
            $this->businessRequests->removeElement($businessRequest);
            // set the owning side to null (unless already changed)
            if ($businessRequest->getUser() === $this) {
                $businessRequest->setUser(null);
            }
        }

        return $this;
    }

    public function getFixedAssessment()
    {
        return $this->fixedAssessment;
    }

    public function setFixedAssessment($fixedAssessment): self
    {
        $this->fixedAssessment = $fixedAssessment;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        //if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addCompany($this);
       // }
        return $this;
    }

    public function removeAllCategories()
    {
        foreach ($this->categories as $category) {
            $this->removeCategory($category);
        }
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeCompany($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(TagInterface $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTag(TagInterface $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag(TagInterface $tag)
    {
        return $this->tags->contains($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagNames(): array
    {
        return empty($this->tagsText) ? [] : array_map('trim', explode(',', $this->tagsText));
    }

    /**
     * @param string
     */
    public function setTagsText($tagsText)
    {
        $this->tagsText = $tagsText;
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getTagsText()
    {
        $this->tagsText = implode(', ', $this->tags->toArray());

        return $this->tagsText;
    }

    public function getAdvertDescription(): ?AdvertDescription
    {
        return $this->advertDescription;
    }

    public function setAdvertDescription(?AdvertDescription $advertDescription): self
    {
        $this->advertDescription = $advertDescription;

        // set (or unset) the owning side of the relation if necessary
        $newCompany = $advertDescription === null ? null : $this;
        if ($newCompany !== $advertDescription->getCompany()) {
            $advertDescription->setCompany($newCompany);
        }

        return $this;
    }

    /**
     * @return Collection|CouponType[]
     */
    public function getCouponTypes(): Collection
    {
        return $this->couponTypes;
    }

    public function getActiveCouponTypes(): array
    {
        $couponTypes = [];
        foreach ($this->couponTypes as $couponType) {
            if ($couponType->isActive()) {
                $couponTypes[] = $couponType;
            }
        }
        return $couponTypes;
    }

    public function addCouponType(CouponType $couponType): self
    {
        if (!$this->couponTypes->contains($couponType)) {
            $this->couponTypes[] = $couponType;
            $couponType->setCompany($this);
        }

        return $this;
    }

    public function removeCouponType(CouponType $couponType): self
    {
        if ($this->couponTypes->contains($couponType)) {
            $this->couponTypes->removeElement($couponType);
            // set the owning side to null (unless already changed)
            if ($couponType->getCompany() === $this) {
                $couponType->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Coupon[]
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupon(Coupon $coupon): self
    {
        if (!$this->coupons->contains($coupon)) {
            $this->coupons[] = $coupon;
            $coupon->setCompany($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        if ($this->coupons->contains($coupon)) {
            $this->coupons->removeElement($coupon);
            // set the owning side to null (unless already changed)
            if ($coupon->getCompany() === $this) {
                $coupon->setCompany(null);
            }
        }

        return $this;
    }

    public function getCreatorEmail(): ?string
    {
        return $this->creatorEmail;
    }

    public function setCreatorEmail(?string $creatorEmail): self
    {
        $this->creatorEmail = $creatorEmail;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIsProtected(): ?bool
    {
        return $this->isProtected;
    }

    public function setIsProtected(?bool $isProtected): self
    {
        $this->isProtected = $isProtected;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
