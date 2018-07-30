<?php

namespace App\Entity\Company;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\CouponRepository")
 */
class Coupon
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_USED = 'used';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="coupons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\Company", inversedBy="coupons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company\CouponType", inversedBy="coupons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $couponType;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    public function setActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function setUsed()
    {
        $this->status = self::STATUS_USED;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isUsed()
    {
        return $this->status == self::STATUS_USED;
    }

    public function getId()
    {
        return $this->id;
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCouponType(): ?CouponType
    {
        return $this->couponType;
    }

    public function setCouponType(?CouponType $couponType): self
    {
        $this->couponType = $couponType;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
}
