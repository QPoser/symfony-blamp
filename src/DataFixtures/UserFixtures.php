<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Services\AuthService;
use App\Services\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var AuthService
     */
    private $service;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, AuthService $service)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->service = $service;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.ru');
        $user->setPlainPassword('secret');
        $this->service->register($user);
        $this->service->verify($user);
        $user->setRoles([User::ROLE_ADMIN]);
        $manager->persist($user);

        for ($i = 1; $i < 25; $i++) {
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setRoles([User::ROLE_USER]);
            $user->setEmail('user'.$i.'@gmail.ru');
            $user->setPlainPassword('pass' . $i);
            $this->service->register($user);
            $this->service->verify($user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}