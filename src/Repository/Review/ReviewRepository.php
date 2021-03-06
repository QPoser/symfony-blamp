<?php

namespace App\Repository\Review;

use App\Entity\Review\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getActiveReviews()
    {
        return $this->findBy(['status' => Review::STATUS_ACTIVE]);
    }

    public function getWaitReviews()
    {
        return $this->findBy(['status' => Review::STATUS_WAIT]);
    }

    public function getCountOfNewReviews()
    {
        $query = $this->createQueryBuilder('review')
            ->select('count(review.id)')
            ->where('review.status = :status')
            ->setParameter('status', Review::STATUS_WAIT);

        return $query->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return Review[] Returns an array of Review objects
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
    public function findOneBySomeField($value): ?Review
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
