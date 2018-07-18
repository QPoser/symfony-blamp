<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 16.07.18
 * Time: 23:29
 */

namespace App\Services;


use App\Entity\Network;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

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
        $user->setEmailToken(uniqid());
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function registerByNetwork(Network $network): User
    {
        $user = new User();
        $user->setUsername($network->getNetwork() . '_' . $network->getIdentity());
        $user->addNetwork($network);
        $this->manager->persist($user);
        $this->manager->persist($network);
        $this->manager->flush();
        return $user;
    }

    public function requestReset(User $user)
    {
        $user->setResetPasswordToken();
        $this->manager->flush();
    }

    public function reset(User $user)
    {
        $cryptPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->resetPassword($cryptPassword);
        $this->manager->flush();
    }

    public function verify(User $user)
    {
        $user->verify();
        $this->manager->flush();
    }
}