<?php

namespace App\Repository\Advert;

use App\Entity\Advert\AdvertDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AdvertDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvertDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvertDescription[]    findAll()
 * @method AdvertDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AdvertDescription::class);
    }

    public function getWaitDescriptions()
    {
        return $this->findBy(['status' => AdvertDescription::STATUS_WAIT]);
    }

    public function getActiveDescriptions()
    {
        return $this->findBy(['status' => AdvertDescription::STATUS_ACTIVE]);
    }

    public function getCountOfNewAdverts()
    {
        $query = $this->createQueryBuilder('ad')
            ->select('count(ad.id)')
            ->where('ad.status = :status')
            ->setParameter('status', AdvertDescription::STATUS_WAIT);

        return $query->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return AdvertDescription[] Returns an array of AdvertDescription objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdvertDescription
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
