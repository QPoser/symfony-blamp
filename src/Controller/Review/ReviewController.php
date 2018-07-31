<?php

namespace App\Controller\Review;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use App\Form\Company\Review\ReviewCreateForm;
use App\Form\Review\ReviewAddCommentForm;
use App\Services\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reviews")
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
     * @Route("/{id}/comment/add", name="review.add.comment.outside", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(Request $request, Review $review): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = new ReviewComment();

        $form = $this->createForm(ReviewAddCommentForm::class, $comment);

        if ($review->getCompany()->getBusinessUsers()->contains($this->getUser())) {
            $form->add('isCompany');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addComment($review, $comment, $this->getUser());

            $this->addFlash('notice', 'Комментарий успешно добавлен.');

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
            if ($request->request->get('_isCompany') == 'isCompany')
            {$comment->setIsCompany(true);}
            $this->service->addComment($review, $comment, $this->getUser());

            $this->addFlash('notice', 'Комментарий успешно добавлен.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        $this->addFlash('warning', 'Неправильный CSRF-Токен.');

        return $this->redirectToRoute('review.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/{id}", name="review.show", methods="GET")
     * @param Review $review
     * @return Response
     */
    public function show(Review $review): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $review);

        return $this->render('review/show.html.twig', ['review' => $review]);
    }

    /**
     * @Route("/edit/{id}", name="review.edit", methods="GET|POST")
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function edit(Request $request, Review $review): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $review);

        $form = $this->createForm(ReviewCreateForm::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->edit($review);

            $this->addFlash('notice', 'Отзыв ' . $review->getId() . ' был успешно обновлен.');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remove/{id}", name="review.delete", methods={"DELETE"})
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function delete(Request $request, Review $review): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $review);

        if ($this->isCsrfTokenValid('delete-review', $request->request->get('token'))) {
            $this->service->delete($review);

            $this->addFlash('notice', 'Отзыв ' . $review->getId() . ' был успешно удален.');

            return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
        }

        $this->addFlash('warning', 'Неправильный CSRF-Токен.');

        return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
    }

    /**
     * @Route("/verify/{id}", name="review.verify")
     * @param Review $review
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verify(Review $review)
    {
        $this->denyAccessUnlessGranted('VERIFY', $review);

        $this->service->verify($review);

        $this->addFlash('notice', 'Данный отзыв успешно проверифицирован');

        return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
    }

    /**
     * @Route("/reject/{id}", name="review.reject")
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function reject(Request $request, Review $review)
    {
        $this->denyAccessUnlessGranted('VERIFY', $review);

        $form = $this->createFormBuilder($review)
            ->add('rejectReason', TextType::class)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->reject($review);

            $this->addFlash('notice', 'Данный отзыв успешно отклонен');

            return $this->redirectToRoute('review.show', ['id' => $review->getId()]);
        }

        return $this->render('review/reject.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
