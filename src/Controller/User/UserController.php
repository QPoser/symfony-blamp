<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\User\UserRepository;
use App\Services\AuthService;
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
     * @Route("", name="user.index", methods="GET")
     */
    public function index(Request $request): Response
    {
        try {
            $this->guardAdmin();
        } catch (\DomainException $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->redirectToRoute('homepage');
        }

        $users = $this->users->search($request->get('search'), $request->get('page') ?: 1);

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($users->count() / 15);

        return $this->render('user/index.html.twig', compact('users', 'thisPage', 'maxPages'));
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
     * @Route("/remove/{id}", name="user.delete")
     * @param User $user
     * @return Response
     */
    public function remove(User $user): Response
    {
        try {
            $this->guardAdmin();
        } catch (\DomainException $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->redirectToRoute('homepage');
        }

        if ($this->getUser() == $user) {
            $this->addFlash('warning', 'Ты что делаешь?');

            return $this->redirectToRoute('user.index');
        }

        $this->service->removeUser($user);

        $this->addFlash('notice', 'Пользователь ' . $user->getUsername() . ' был успешно удален.');

        return $this->redirectToRoute('user.index');
    }

    /**
     * @Route("/verify/{id}", name="user.verify")
     * @param User $user
     * @param AuthService $authService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verifyUser(User $user, AuthService $authService)
    {
        try {
            $this->guardAdmin();
        } catch (\DomainException $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->redirectToRoute('homepage');
        }

        $authService->verify($user);

        $this->addFlash('notice', 'Пользователь ' . $user->getUsername() . ' был успешно проверифицирован.');

        return $this->redirectToRoute('user.index');
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

    private function guardAdmin()
    {
        if (!$this->getUser() || !$this->getUser()->isAdmin()) {
            throw new \DomainException('У вас нет доступа для просмотра данной страницы');
        }
    }
}
