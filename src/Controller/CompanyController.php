<?php

namespace App\Controller;

use App\Entity\Company\Company;
use App\Form\Company\CompanyCreateForm;
use App\Form\Company\CompanyEditForm;
use App\Repository\CompanyRepository;
use App\Services\CompanyService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CompanyController
 * @package App\Controller
 * @Route("/company")
 */
class CompanyController extends Controller
{
    /**
     * @var CompanyService
     */
    private $service;
    /**
     * @var CompanyRepository
     */
    private $repository;

    public function __construct(CompanyService $service, CompanyRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="company")
     */
    public function index()
    {
        $companies = $this->repository->findAll();

        return $this->render('company/index.html.twig', compact('companies'));
    }

    /**
     * @Route("/create", name="company.create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $company = new Company();

        $form = $this->createForm(CompanyCreateForm::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $company = $this->service->create($company, $form);

            $this->addFlash('notice', 'Company ' . $company->getName() . ' successfully added.');

            return $this->redirectToRoute('company');
        }

        return $this->render('company/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="company.show")
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Company $company)
    {
        return $this->render('company/show.html.twig', compact('company'));
    }


    /**
     * @Route("/edit/{id}", name="company.edit")
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Company $company)
    {
        $form = $this->createForm(CompanyEditForm::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $this->service->edit($company, $form);

            $this->addFlash('notice', 'Company ' . $company->getName() . ' successfully edited.');

            return $this->redirectToRoute('company.show', ['id' => $company->getId()]);
        }

        return $this->render('company/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/remove/{id}", name="company.remove")
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Company $company)
    {
        $this->service->remove($company);

        $this->addFlash('warning', 'Company ' . $company->getName() . ' successfully deleted.');

        return $this->redirectToRoute('company');
    }
}
