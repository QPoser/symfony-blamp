<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\ReviewComment;
use App\Form\Review\ReviewAddCommentForm;
use App\Services\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review")
 */
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
     */
    public function addComment(Request $request, Review $review): Response
    {
        $comment = new ReviewComment();

        $form = $this->createForm(ReviewAddCommentForm::class, $comment);

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
     * @Route("/{id}", name="review_show", methods="GET")
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', ['review' => $review]);
    }

    /**
     * @Route("/{id}/edit", name="review_edit", methods="GET|POST")
     */
    public function edit(Request $request, Review $review): Response
    {
        $form = $this->createForm(ReviewAddCommentForm::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('review_edit', ['id' => $review->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_delete", methods="DELETE")
     */
    public function delete(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        return $this->redirectToRoute('review_index');
    }
}
