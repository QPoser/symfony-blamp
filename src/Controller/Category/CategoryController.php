<?php

namespace App\Controller\Category;

use App\Entity\Category\Category;
use App\Form\Category\CategoryType;
use App\Repository\Company\CategoryRepository;
use App\Services\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories")
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private $service;
    /**
     * @var CategoryRepository
     */
    private $repository;

    public function __construct(CategoryService $categoryService, CategoryRepository $repository)
    {
        $this->service = $categoryService;
        $this->repository = $repository;
    }


    /**
     * @Route("/", name="category.index", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', ['categories' => $this->repository->findAll()]);
    }

    /**
     * @Route("/new", name="category.new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create($category);
            $this->repository->generateRoad();
            $this->service->flusher();

            return $this->redirectToRoute('category.index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category.show", methods="GET")
     * @param Category $category
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Category $category, Request $request)
    {
        $companies = $this->repository->search($category, $request->get('search'), $request->get('page') ?: 1);

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($companies->count() / 4);

        return $this->render('category/show.html.twig', compact('companies', 'category', 'maxPages', 'thisPage'));
    }

//    /**
//     * @Route("/{id}", name="category.show", methods="GET")
//     * @param Category $category
//     * @return Response
//     */
//    public function show(Category $category): Response
//    {
//        return $this->render('category/show.html.twig', ['category' => $category]);
//    }

    /**
     * @Route("/{id}/edit", name="category.edit", methods="GET|POST")
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->edit($category);
            $this->repository->generateRoad();
            $this->service->flusher();

            return $this->redirectToRoute('category.edit', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category.delete", methods="DELETE")
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->service->delete($category);
            $this->repository->generateRoad();
            $this->service->flusher();
        }

        return $this->redirectToRoute('category.index');
    }
}
