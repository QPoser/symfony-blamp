<?php

namespace App\Controller\Review;

use App\Entity\Review\Like;
use App\Entity\Review\Review;
use App\Repository\Review\LikeRepository;
use App\Services\LikeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review")
 */
class LikeController extends Controller
{
    /**
     * @var LikeService
     */
    private $service;

    public function __construct(LikeService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/{id}/like", name="review.like", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addLike(Request $request, Review $review): Response
    {
        if ($this->service->addLike($review)) {
            $this->addFlash('notice', 'Лайк успешно добавлен.');
        }else {
            $this->addFlash('notice', 'Лайк успешно удален.');
        }
        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/{id}/dislike", name="review.dislike", methods="GET|POST")
     * @param Review $review
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addDislike(Review $review): Response
    {
        if ($this->service->addLike($review, Like::DISLIKE)) {
        $this->addFlash('notice', 'Дизлайк успешно добавлен.');
    }else {
        $this->addFlash('notice', 'Дизлайк успешно удален.');
    }
        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }


    /**
     * @Route("/{id}", name="review_like_delete", methods="DELETE")
     * @param Request $request
     * @param Like $like
     * @return Response
     */
    public function delete(Request $request, Like $like): Response
    {
        if ($this->isCsrfTokenValid('delete'.$like->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($like);
            $em->flush();
        }

        return $this->redirectToRoute('review_like_index');
    }
}
