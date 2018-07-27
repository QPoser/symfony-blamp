<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\User\UserRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @var \App\Repository\User\UserRepository
     */
    private $users;
    /**
     * @var UserService
     */
    private $service;

    public function __construct(UserRepository $users, UserService $service)
    {
        $this->users = $users;
        $this->service = $service;
    }

    /**
     * @Route("/", name="user.index", methods="GET")
     */
    public function index(): Response
    {
        $users = $this->users->findAll();

        return $this->render('user/index.html.twig', compact('users'));
    }

    /**
     * @Route("/{id}", name="user.show", methods="GET")
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/subs/{id}/subscribe", name="user.subscribe")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribe(User $user)
    {
        $this->service->addSubscriber($user, $this->getUser());

        $this->addFlash('notice', 'Вы успешно подписались на пользователя '  . $user->getUsername());

        return $this->redirectToRoute('user.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("/subs/{id}/unsubscribe", name="user.unsubscribe")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unsubscribe(User $user)
    {
        $this->service->cancelSubscribe($user, $this->getUser());

        $this->addFlash('notice', 'Вы успешно отписались от пользователя '  . $user->getUsername());

        return $this->redirectToRoute('user.show', ['id' => $user->getId()]);
    }
}
