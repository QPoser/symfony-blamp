<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use App\Form\Company\Review\ReviewCreateForm;
use App\Form\Review\ReviewAddCommentForm;
use App\Services\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ReviewController extends Controller
{
    /**
     * @var ReviewService
     */
    private $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/{id}/comments/add", name="review.add.comment", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(Request $request, Review $review): Response
    {
        $comment = new ReviewComment();

        $form = $this->createForm(ReviewAddCommentForm::class, $comment);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $this->service->addComment($review, $comment);

            $this->addFlash('notice', 'Comment is successfully added.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        return $this->render('review/comment/add.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reviews/{id}", name="review.show", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function show(Request $request, Review $review): Response
    {
        $comment = new ReviewComment();

        $form = $this->createForm(ReviewAddCommentForm::class, $comment);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $this->service->addComment($review, $comment);

            $this->addFlash('notice', 'Comment is successfully added.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId(),
                'form' => $form->createView(),
                ]);
        }

        return $this->render('review/comment/add.html.twig', ['review' => $review,
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/reviews/edit/{id}", name="review.edit", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function edit(Request $request, Review $review): Response
    {
        $form = $this->createForm(ReviewCreateForm::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->edit($review);

            $this->addFlash('notice', 'Review ' . $review->getId() . ' has been successfully update.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reviews/remove/{id}", name="review.delete", methods={"DELETE"})
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function delete(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('delete-review', $request->request->get('token'))) {
            $this->service->delete($review);

            $this->addFlash('notice', 'Review ' . $review->getId() . ' has been successfully deleted.');

            return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
        }

        $this->addFlash('warning', 'Invalid csrf token for delete.');

        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/reviews/{id}/like", name="review.like", methods="GET")
     * @param Review $review
     * @return Response
     */
    public function addLike(Review $review): Response
    {
        $user = $this->getUser();
        if ($this->service->findLikeByUser($review, $user)) {
            $this->addFlash('notice', 'Like is successfully removed.');
            return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
        }

        $this->service->addLike($review, $user);

        $this->addFlash('notice', 'Like is successfully added.');

        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/reviews/{id}/dislike", name="review.dislike", methods="GET")
     * @param Review $review
     * @return Response
     */
    public function addDislike(Review $review): Response
    {
        $user = $this->getUser();
        if ($this->service->findLikeByUser($review, $user, Like::DISLIKE)) {
            $this->addFlash('notice', 'Dislike is successfully removed.');
            return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
        }

        $this->service->addLike($review, $user, Like::DISLIKE);

        $this->addFlash('notice', 'Dislike is successfully added.');

        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }
}
