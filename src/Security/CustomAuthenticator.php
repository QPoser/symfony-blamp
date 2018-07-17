<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $users, RouterInterface $router)
    {
        $this->encoder = $encoder;
        $this->users = $users;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        if (!$username && !$password) {
            return null;
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $username
        );

        return compact('username', 'password');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
            $username = $credentials['username'];

            return $this->users->findUserByUsername($username);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];

        /** @var User $user */
        if ($this->encoder->isPasswordValid($user, $password)) {
            if ($user->isActive()) {
                return true;
            }
            throw new AuthenticationException('Пользователь неактивен, проверьте вашу почту.');
        }

        throw new AuthenticationException('Вы ввели неверный логин или пароль');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $session = new Session();

        $session->getFlashBag()->add('warning', $exception->getMessage());

        return new RedirectResponse($this->getLoginUrl());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->getDefaultSuccessRedirectUrl();
        }

        return new RedirectResponse($targetPath);
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }
}
