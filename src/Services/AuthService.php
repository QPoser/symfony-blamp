<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 16.07.18
 * Time: 23:29
 */

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthService
{
    private $passwordEncoder;
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
    }

    public function register(User $user)
    {
        $cryptPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($cryptPassword);
        $this->manager->persist($user);
        $this->manager->flush();
    }
}