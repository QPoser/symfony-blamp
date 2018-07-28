<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 28.07.18
 * Time: 15:30
 */

namespace App\Services;


use App\Entity\Company\Company;
use App\Entity\Company\Coupon;
use App\Entity\Company\CouponType;
use Doctrine\ORM\EntityManager;

class CouponService
{

    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function acceptCoupon(Coupon $coupon)
    {
        $coupon->setUsed();

        $this->manager->flush();
    }

    public function addCouponType(CouponType $couponType, Company $company)
    {
        $couponType->setCompany($company);
        $couponType->setStatus(CouponType::STATUS_ON_MODERATION);

        $this->manager->persist($couponType);
        $this->manager->flush();
    }

    public function rejectCoupon(CouponType $couponType)
    {
        $couponType->reject();

        $this->manager->flush();
    }

    public function verifyCoupon(CouponType $couponType)
    {
        $couponType->verify();

        $this->manager->flush();
    }

    public function closeCoupon(CouponType $couponType)
    {
        if (!$couponType->isActive()) {
            throw new \DomainException('Вы не можете закрыть неактивный купон!');
        }

        $couponType->close();

        $this->manager->flush();
    }

    public function openCoupon(CouponType $couponType)
    {
        if (!$couponType->isClosed()) {
            throw new \DomainException('Вы не можете открыть незакрытый купон!');
        }

        $couponType->open();

        $this->manager->flush();
    }

}