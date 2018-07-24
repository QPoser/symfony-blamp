<?php

namespace App\Repository;

use App\Entity\Review\ReviewPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReviewPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewPhoto[]    findAll()
 * @method ReviewPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewPhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReviewPhoto::class);
    }

//    /**
//     * @return ReviewPhoto[] Returns an array of ReviewPhoto objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReviewPhoto
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
