<?php

namespace App\Repository\Company;

use App\Entity\Company\Protector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Protector|null find($id, $lockMode = null, $lockVersion = null)
 * @method Protector|null findOneBy(array $criteria, array $orderBy = null)
 * @method Protector[]    findAll()
 * @method Protector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProtectorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Protector::class);
    }

    public function findRandom()
    {
        $protectors = $this->findAll();

        if (empty($protectors)) {
            return null;
        }

        return $protectors[array_rand($protectors)];
    }

//    /**
//     * @return Protecter[] Returns an array of Protecter objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Protecter
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
