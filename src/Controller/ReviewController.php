<?php

namespace App\Controller;

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

            $this->addFlash('notice', 'Review ' . $review->getId() . ' has been successfully update.');

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

            $this->addFlash('notice', 'Review ' . $review->getId() . ' has been successfully deleted.');

            return $this->redirectToRoute('company.show', ['id' => $review->getCompany()->getId()]);
        }

        $this->addFlash('warning', 'Invalid csrf token for delete.');

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
