<?php

namespace App\Security;

use App\Entity\Network;
use App\Entity\User;
use App\Repository\User\UserRepository;
use App\Services\AuthService;
use App\Services\NetworkService;
use GuzzleHttp\Client;
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
    /**
     * @var AuthService
     */
    private $authService;
    /**
     * @var NetworkService
     */
    private $networkService;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $users, RouterInterface $router, AuthService $authService, NetworkService $networkService)
    {
        $this->encoder = $encoder;
        $this->users = $users;
        $this->router = $router;
        $this->authService = $authService;
        $this->networkService = $networkService;
    }

    public function supports(Request $request)
    {
        return ( $request->getPathInfo() == '/login' && $request->isMethod('POST') )
            || ( $request->getPathInfo() == '/login/check-vkontakte' && $request->query->has('code') );
    }

    public function getCredentials(Request $request)
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        if (!$username && !$password) {
            $code = $request->query->get('code');
            if ($code) {
                return compact('code');
            }
            throw new AuthenticationException('Вы ввели некорректные данные.');
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $username
        );

        return compact('username', 'password');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
            if (!empty($credentials['code'])) {
                $network = $this->networkService->getNetworkVkByCode($credentials['code']);
                $user = $this->users->findUserByNetworkIdentity($network->getIdentity());
                if (!$user) {
                    $user = $this->authService->registerByNetwork($network);
                }
                return $user;
            }
            $username = $credentials['username'];

            if (!$user = $this->users->findUserByUsername($username)) {
                throw new AuthenticationException('Пользователь не найден.');
            }
            return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!empty($credentials['code'])) {
            return true;
        }

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
