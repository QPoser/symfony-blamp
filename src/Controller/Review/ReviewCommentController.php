<?php

namespace App\Controller\Review;

use App\Entity\Review\ReviewComment;
use App\Form\Review\ReviewCommentType;
use App\Services\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review/review/comment")
 */
class ReviewCommentController extends Controller
{
    /**
     * @var CommentService
     */
    private $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/{id}/answer", name="comment.answer", methods="GET|POST")
     * @param Request $request
     * @param ReviewComment $reviewComment
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function new(Request $request, ReviewComment $reviewComment): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = new ReviewComment();
        $form = $this->createForm(ReviewCommentType::class, $comment);

        if ($reviewComment->getReview()->getCompany()->getBusinessUsers()->contains($this->getUser())) {
            $form->add('isCompany', CheckboxType::class, array(
                'label'    => 'Оставить комментарий от имени компании',
                'required' => false,
                ));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addComment($reviewComment, $comment);


            $this->addFlash('notice', 'Комментарий успешно добавлен.');


            return $this->redirectToRoute('review.show', ['id' => $reviewComment->getReview()->getId()]);
        }

        return $this->render('review/review_comment/new.html.twig', [
            'review_comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment.edit", methods="GET|POST")
     * @param Request $request
     * @param ReviewComment $reviewComment
     * @return Response
     */
    public function edit(Request $request, ReviewComment $reviewComment): Response
    {
        $form = $this->createForm(ReviewCommentType::class, $reviewComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->edit($reviewComment);

            $this->addFlash('notice', 'Комментарий ' . $reviewComment->getId() . ' был успешно обновлен.');

            return $this->redirectToRoute('review.show', ['id' => $reviewComment->getReview()->getId()]);
        }

        return $this->render('review/review_comment/edit.html.twig', [
            'review_comment' => $reviewComment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="comment.delete", methods="DELETE")
     * @param Request $request
     * @param ReviewComment $reviewComment
     * @return Response
     */
    public function delete(Request $request, ReviewComment $reviewComment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reviewComment->getId(), $request->request->get('_token'))) {
            $reviewId = $reviewComment->getReview()->getId();
            $commentId = $reviewComment->getId();
            $this->service->delete($reviewComment);
        }

        $this->addFlash('notice', 'Комментарий ' . $commentId . ' был успешно удален.');

        return $this->redirectToRoute('review.show', ['id' => $reviewId]);
    }
}
