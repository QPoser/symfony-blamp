<?php

namespace App\Repository\Advert;

use App\Entity\Advert\Banner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    public function getActiveBanners()
    {
        return $this->findBy(['status' => Banner::STATUS_ACTIVE]);
    }

    public function getWaitBanners()
    {
        return $this->findBy(['status' => Banner::STATUS_WAIT]);
    }

    public function getVerticalBanners()
    {
        return $this->findBy(['status' => Banner::STATUS_ACTIVE, 'format' => Banner::FORMAT_VERTICAL]);
    }

    public function getHorizontalBanners()
    {
        return $this->findBy(['status' => Banner::STATUS_ACTIVE, 'format' => Banner::FORMAT_HORIZONTAL]);
    }

//    /**
//     * @return Banner[] Returns an array of Banner objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Banner
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
