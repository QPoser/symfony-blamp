<?php

namespace App\Repository\Company;

use App\Entity\Category\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getPreferredCategories()
    {
        $preferredArray = array();
        $categories = $this->findBy(['level' => 0]);
        $this->fetchCategories($categories, $preferredArray);
        return $preferredArray;
    }

    public function fetchCategories($categories, array $preferredArray)
    {
        /**
         * @var $category Category
         */
        foreach ($categories as $category) {
            array_push($preferredArray, $categories);
            if ($category->getChildrenCategories() == null) {
                $this->fetchCategories($category->getChildrenCategories(), $preferredArray);
            }
        }
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
