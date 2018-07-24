<?php

namespace App\Repository\Company;

use App\Entity\Company\BusinessRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BusinessRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessRequest[]    findAll()
 * @method BusinessRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BusinessRequest::class);
    }

//    /**
//     * @return BusinessRequest[] Returns an array of BusinessRequest objects
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
    public function findOneBySomeField($value): ?BusinessRequest
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
