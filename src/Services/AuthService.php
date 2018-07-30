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
use App\Services\App\EmailService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class AuthService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * AuthService constructor.
     * @param EntityManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EmailService $emailService
     */
    public function __construct(EntityManager $manager, UserPasswordEncoderInterface $passwordEncoder, EmailService $emailService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->emailService = $emailService;
    }

    public function register(User $user)
    {
        $user->setRoles([User::ROLE_USER]);
        $cryptPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($cryptPassword);
        $user->setEmailToken(uniqid());

        $this->emailService->sendMail('email/regiter_verify.html.twig', 'Поздравляем с регистрацией на сайте Blamp!',
            ['username' => $user->getUsername(), 'token' => $user->getEmailToken()],
            $user->getEmail());

        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function registerByNetwork(Network $network): User
    {
        $user = new User();
        $user->setRoles([User::ROLE_USER]);
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

        $this->emailService->sendMail('email/password_reset.html.twig', 'Сброс пароля для аккаунта ' . $user->getUsername(),
            ['username' => $user->getUsername(), 'token' => $user->getPasswordResetToken()],
            $user->getEmail());

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