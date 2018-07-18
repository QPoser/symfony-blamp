<?php

namespace App\Repository;

use App\Entity\ReviewComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Gedmo\Tree\Traits\NestedSetEntity;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @method ReviewComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewComment[]    findAll()
 * @method ReviewComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewCommentRepository extends NestedTreeRepository //ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReviewComment::class);
    }

//    /**
//     * @return ReviewComment[] Returns an array of ReviewComment objects
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
    public function findOneBySomeField($value): ?ReviewComment
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
