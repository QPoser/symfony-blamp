<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Services\EventService;
use App\Services\UserService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CabinetController
 * @package App\Controller
 * @Route("/cabinet")
 */
class CabinetController extends Controller
{
    /**
     * @Route("/", name="cabinet")
     * @param EventService $eventService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EventService $eventService)
    {
        $eventService->setSeenForUser($this->getUser());

        return $this->render('cabinet/index.html.twig', []);
    }

    /**
     * @Route("/reviews", name="cabinet.reviews")
     * @param ReviewRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reviews(ReviewRepository $repository)
    {
        $reviews = $this->getUser()->getReviews();

        return $this->render('cabinet/reviews.html.twig', compact('reviews'));
    }

    /**
     * @Route("/profile", name="cabinet.profile")
     * @param ReviewRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profile(ReviewRepository $repository)
    {
        $reviews = $repository->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('cabinet/profile.html.twig', compact('reviews'));
    }

    /**
     * @Route("/set-business", name="cabinet.set-business", methods={"POST"})
     * @param UserService $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setBusiness(UserService $service)
    {
        $service->setBusiness();

        $this->addFlash('notice', 'Вы успешно стали бизнес-пользователем');

        return $this->redirectToRoute('cabinet.profile');
    }

    /**
     * @Route("/unset-business", name="cabinet.unset-business", methods={"POST"})
     * @param UserService $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unsetBusiness(UserService $service)
    {
        $service->unsetBusiness();

        $this->addFlash('notice', 'Вы перестали быть бизнес-пользователем');

        return $this->redirectToRoute('cabinet.profile');
    }

    /**
     * @Route("/business/profile", name="cabinet.business.profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function businessProfile()
    {
        return $this->render('cabinet/business/index.html.twig');
    }

    /**
     * @Route("/business/adverts", name="cabinet.business.adverts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function businessAdverts()
    {
        return $this->render('cabinet/business/advert.html.twig');
    }
}
