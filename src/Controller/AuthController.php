<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Auth\RegisterFormType;
use App\Repository\UserRepository;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}
