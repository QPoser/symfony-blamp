<?php

namespace App\Repository\Advert;

use App\Entity\Advert\LogBanner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogBanner|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogBanner|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogBanner[]    findAll()
 * @method LogBanner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogBannerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogBanner::class);
    }

//    /**
//     * @return LogBanner[] Returns an array of LogBanner objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogBanner
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
