<?php

namespace App\Repository\User;

use App\Entity\User;
use App\Repository\User\NetworkRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @var NetworkRepository
     */
    private $networks;

    public function __construct(RegistryInterface $registry, NetworkRepository $networks)
    {
        parent::__construct($registry, User::class);
        $this->networks = $networks;
    }

    public function findUserByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function findUserByEmailToken($token)
    {
        return $this->findOneBy(['emailToken' => $token]);
    }

    public function findUserByPasswordResetToken($token)
    {
        return $this->findOneBy(['passwordResetToken' => $token]);
    }

    public function search($search = null, $page = 1)
    {
        $query = $this->createQueryBuilder('user')
            ->where('user.username LIKE :search')
            ->setParameters([
                'search' => '%' . $search . '%',
            ])
        ;

        $paginator = $this->paginate($query->getQuery(), $page ?: 1);

        return $paginator;
    }

    public function findUserByNetworkIdentity($identity)
    {
        $network = $this->networks->findByIdentity('vk', $identity);
        if (!$network) {
            return null;
        }
        return $network->getUser();
    }

    public function paginate($dql, $page = 1, $limit = 15)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
