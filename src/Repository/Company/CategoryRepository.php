<?php

namespace App\Repository\Company;

use App\Entity\Category\Category;
use App\Entity\Company\Company;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function generateRoad() {
        $categories = $this->findBy(['level' => 0]);
        $i = 0;
        $this->fetchNum($categories, $i);
    }

    public function fetchNum($categories, &$num)
    {
        /**
         * @var $category Category
         */
        foreach ($categories as $category) {
            $category->setNum($num);
            $num++;
            if ($category->getChildrenCategories() != null) {
                $this->fetchNum($category->getChildrenCategories(), $num);
            }
        }
    }


    public function search(Category $category, $search = null, $page = 1)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('company')
            ->from(Company::class, 'company')
            ->leftJoin(Category::class, 'category', 'with', 'company MEMBER OF category.companies')
            ->leftJoin(Tag::class, 'tag', 'with', 'company MEMBER OF tag.companies')
            ->where('company.name LIKE :search  OR category.name LIKE :search OR tag.name LIKE :search OR company.description LIKE :search')
            ->andWhere('company.status = :status')
            ->andWhere('category.id = ' . $category->getId())
            ->orderBy('company.name', 'ASC')
            ->setParameters([
                'search' => '%' . $search . '%',
                'status' => Company::STATUS_ACTIVE,
            ])
        ;

        $paginator = $this->paginate($query->getQuery(), $page ?: 1);

        return $paginator;
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
//     * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
