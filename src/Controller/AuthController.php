<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Auth\RegisterFormType;
use App\Form\Auth\ResetPasswordForm;
use App\Repository\UserRepository;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthController extends Controller
{

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var AuthService
     */
    private $service;

    public function __construct(AuthenticationUtils $authenticationUtils, UserRepository $repository, AuthService $service)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        try {
            $this->denyAccessUnlessGranted('AUTH', null);
        } catch (AccessDeniedException $e) {
            $this->addFlash('notice', $e->getMessage());

            return $this->redirectToRoute('homepage');
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();

        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->register($user);

            $this->addFlash('notice', 'Вы успешно зарегистрировались, проверьте ваш email!');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/", name="reset.password.request")
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function requestReset(Request $request)
    {
        if ($username = $request->request->get('_username')) {
            if ($user = $this->repository->findUserByUsername($username)) {
                $this->service->requestReset($user);
                $this->addFlash('notice', 'Инструкции по смене пароля отправлены вам на почту!');
                return $this->redirectToRoute('login');
            }
            dump($user);
            dump($username);
            die;
            $this->addFlash('warning', 'Пользователь с данным именем не найден');
        }

        return $this->render('auth/request_reset.html.twig');
    }

    /**
     * @Route("/reset/{token}", name="reset.password")
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request, string $token)
    {
        $user = $this->repository->findUserByPasswordResetToken($token);

        if (!$user) {
            $this->addFlash('warning', 'Неправильный токен!');
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ResetPasswordForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->reset($user);

            $this->addFlash('notice', 'Вы успешно сбросили пароль!');

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/{token}", name="verify")
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verifyToken(string $token)
    {
        //$this->generateUrl('verify', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $this->repository->findUserByEmailToken($token);

        if (!$user) {
            $this->addFlash('warning', 'Неправильный токен!');
            return $this->redirectToRoute('login');
        }

        $this->service->verify($user);

        $this->addFlash('notice', 'Вы успешно подтвердили свою почту!');

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}
