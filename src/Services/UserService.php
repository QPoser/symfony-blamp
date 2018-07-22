<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 19.07.18
 * Time: 19:35
 */

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserService
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(EntityManager $manager, TokenStorage $storage)
    {
        $this->manager = $manager;
        $this->storage = $storage;
    }

    public function editUser(User $user)
    {
        $this->manager->flush();
    }

    public function setBusiness()
    {
        $user = $this->storage->getToken()->getUser();
        $user->becomeBusiness();
        $this->manager->flush();

        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $this->storage->setToken($token);
    }

    public function unsetBusiness()
    {
        $user = $this->storage->getToken()->getUser();
        $user->becomeUser();
        $this->manager->flush();

        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $this->storage->setToken($token);
    }

}