<?php

namespace App\Repository\Company;

use App\Entity\Company\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getActiveCompanies()
    {
        return $this->findBy(['status' => Company::STATUS_ACTIVE]);
    }

    public function getWaitCompanies()
    {
        return $this->findBy(['status' => Company::STATUS_WAIT]);
    }

    public function search(string $search)
    {
        $query = $this->createQueryBuilder('company')
            ->where('company.name LIKE :search')
            ->andWhere('company.status LIKE :status')
            ->setParameters([
                'search' => '%' . $search . '%',
                'status' => Company::STATUS_ACTIVE,
            ])
            ->getQuery();
        return $query->getResult();
    }
//    /**
//     * @return Company[] Returns an array of Company objects
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
    public function findOneBySomeField($value): ?Company
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
