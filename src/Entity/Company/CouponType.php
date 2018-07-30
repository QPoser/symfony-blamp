<?php

namespace App\Entity\Company;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\CouponTypeRepository")
 */
class CouponType
{
    const STATUS_ON_MODERATION = 'onModeration';
    const STATUS_CLOSED = 'closed';
    const STATUS_ACTIVE = 'active';
    const STATUS_REJECTED = 'rejected';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\Company", inversedBy="couponTypes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company\Coupon", mappedBy="couponType")
     */
    private $coupons;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
    }

    public function isClosed()
    {
        return $this->status == self::STATUS_CLOSED;
    }

    public function isRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isOnModeration()
    {
        return $this->status == self::STATUS_ON_MODERATION;
    }

    public function verify()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function close()
    {
        $this->status = self::STATUS_CLOSED;
    }

    public function open()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $coupon->setCouponType($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        if ($this->coupons->contains($coupon)) {
            $this->coupons->removeElement($coupon);
            // set the owning side to null (unless already changed)
            if ($coupon->getCouponType() === $this) {
                $coupon->setCouponType(null);
            }
        }

        return $this;
    }
}
