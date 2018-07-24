<?php

namespace App\Controller;

use App\Entity\ReviewComment;
use App\Form\ReviewCommentType;
use App\Repository\ReviewCommentRepository;
use App\Services\CommentService;
use App\Services\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review/comments")
 */
class ReviewCommentController extends Controller
{
    /**
     * @var ReviewService
     */
    private $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("{id}/add", name="review.comment.add", methods="GET|POST")
     * @param Request $request
     * @param ReviewComment $parentComment
     * @return Response
     */
    public function addComment(Request $request, ReviewComment $parentComment): Response
    {
        $comment = new ReviewComment();
        $form = $this->createForm(ReviewCommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUser($user);
            $this->service->addComment($parentComment, $comment);

            $this->addFlash('notice', 'Comment is successfully added.');

            return $this->redirectToRoute('review.show', ['id' => $parentComment->getReview()->getId()]);
        }

        return $this->render('review_comment/new.html.twig', [
            'review_comment' => $parentComment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="review_comment_edit", methods="GET|POST")
     */
    public function edit(Request $request, ReviewComment $reviewComment): Response
    {
        $form = $this->createForm(ReviewCommentType::class, $reviewComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('review_comment_edit', ['id' => $reviewComment->getId()]);
        }

        return $this->render('review_comment/edit.html.twig', [
            'review_comment' => $reviewComment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_comment_delete", methods="DELETE")
     */
    public function delete(Request $request, ReviewComment $reviewComment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reviewComment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reviewComment);
            $em->flush();
        }

        return $this->redirectToRoute('review_comment_index');
    }
}
