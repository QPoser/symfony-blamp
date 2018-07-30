<?php

namespace App\Repository\Company;

use App\Entity\Category\Category;
use App\Entity\Company\Company;
use App\Entity\Company\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function search(string $search = null, $page = 1)
    {
        $query = $this->createQueryBuilder('company')
            ->leftJoin(Category::class, 'category', 'with', 'company MEMBER OF category.companies')
            ->leftJoin(Tag::class, 'tag', 'with', 'company MEMBER OF tag.companies')
            ->where('company.name LIKE :search OR category.name LIKE :search OR tag.name LIKE :search OR company.description LIKE :search')
            ->andWhere('company.status = :status')
            ->orderBy('company.name', 'ASC')
            ->setParameters([
                'search' => '%' . $search . '%',
                'status' => Company::STATUS_ACTIVE,
            ])
        ;

        $paginator = $this->paginate($query->getQuery(), $page ?: 1);

        return $paginator;
    }

    public function getCountOfNewCompanies()
    {
        $query = $this->createQueryBuilder('company')
            ->select('count(company.id)')
            ->where('company.status = :status')
            ->setParameter('status', Company::STATUS_WAIT);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function paginate($dql, $page = 1, $limit = 4)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
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
