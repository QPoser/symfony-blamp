<?php

namespace App\Controller\Review;

use App\Entity\Review\Like;
use App\Form\Review\LikeType;
use App\Repository\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review/like")
 */
class LikeController extends Controller
{
    /**
     * @Route("/", name="review_like_index", methods="GET")
     */
    public function index(LikeRepository $likeRepository): Response
    {
        return $this->render('review_like/index.html.twig', ['likes' => $likeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="review_like_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $like = new Like();
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();

            return $this->redirectToRoute('review_like_index');
        }

        return $this->render('review_like/new.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_like_show", methods="GET")
     */
    public function show(Like $like): Response
    {
        return $this->render('review_like/show.html.twig', ['like' => $like]);
    }

    /**
     * @Route("/{id}/edit", name="review_like_edit", methods="GET|POST")
     */
    public function edit(Request $request, Like $like): Response
    {
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('review_like_edit', ['id' => $like->getId()]);
        }

        return $this->render('review_like/edit.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_like_delete", methods="DELETE")
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
