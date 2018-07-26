<?php

namespace App\Controller;

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
     * @Route("/{id}/comment/add", name="review.add.comment.outside", methods="GET|POST")
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

        if ($review->getCompany()->getBusinessUsers()->contains($this->getUser())) {
            $form->add('isCompany');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addComment($review, $comment);

            $this->addFlash('notice', 'Comment is successfully added.');

            return $this->redirectToRoute('company', ['id' => $review->getCompany()->getId()]);
        }

        return $this->render('review/comment/add.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/comments/add", name="review.add.comment.inside", methods="ADD_COMMENT")
     * @param Request $request
     * @param Review $review
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addCommentInReview(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('add'.$review->getId(), $request->request->get('_token'))) {
            $comment = new ReviewComment();
            $comment->setText($request->request->get('_text'));
            $this->service->addComment($review, $comment);
            $this->addFlash('notice', 'Comment ' . $comment->getId() . ' has been successfully added.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        $this->addFlash('warning', 'Invalid csrf token for comment.');

        return $this->redirectToRoute('review.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/reviews/{id}", name="review.show", methods="GET")
     * @param Review $review
     * @return Response
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', ['review' => $review]);
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
}
