<?php

namespace App\Repository\Company;

use App\Entity\Company\Company;
use App\Entity\Company\CouponType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CouponType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CouponType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CouponType[]    findAll()
 * @method CouponType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CouponType::class);
    }

    public function getWaitCoupons()
    {
        return $this->findBy(['status' => CouponType::STATUS_ON_MODERATION]);
    }

    public function getActiveCoupons()
    {
        return $this->findBy(['status' => CouponType::STATUS_ACTIVE]);
    }

    public function findActiveByCompany(Company $company)
    {
        return $this->findBy(['status' => CouponType::STATUS_ACTIVE, 'company' => $company]);
    }

    public function getRandomActiveCouponByCompany(Company $company)
    {
        $coupons = $this->findActiveByCompany($company);

        return $coupons[array_rand($coupons)];
    }

    public function getCountOfNewCoupons()
    {
        $query = $this->createQueryBuilder('coupon')
            ->select('count(coupon.id)')
            ->where('coupon.status = :status')
            ->setParameter('status', CouponType::STATUS_ON_MODERATION);

        return $query->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return CouponType[] Returns an array of CouponType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CouponType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
