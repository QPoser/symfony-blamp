<?php

namespace App\Controller;

use App\Entity\Company\BusinessRequest;
use App\Entity\Company\Company;
use App\Entity\Review\Review;
use App\Form\Company\BusinessRequestForm;
use App\Form\Company\CompanyCreateForm;
use App\Form\Company\CompanyEditForm;
use App\Form\Company\Review\ReviewAddCommentForm;
use App\Form\Company\Review\ReviewCreateForm;
use App\Repository\CompanyRepository;
use App\Services\AdvertService;
use App\Services\CompanyService;
use App\Services\UserService;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $companies = $this->repository->getActiveCompanies();

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
        $this->denyAccessUnlessGranted('SHOW', $company);

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
        $this->denyAccessUnlessGranted('EDIT', $company);

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
        $this->denyAccessUnlessGranted('DELETE', $company);

        $this->service->remove($company);

        $this->addFlash('warning', 'Company ' . $company->getName() . ' successfully deleted.');

        return $this->redirectToRoute('company');
    }


    /**
     * @Route("/verify/{id}", name="company.verify")
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verify(Company $company)
    {
        $this->denyAccessUnlessGranted('VERIFY', $company);

        $this->service->verify($company);

        $this->addFlash('notice', 'Company ' . $company->getName() . ' successfully verified.');

        return $this->redirectToRoute('company');
    }

    /**
     * @Route("/reject/{id}", name="company.reject")
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reject(Request $request, Company $company)
    {
        $this->denyAccessUnlessGranted('VERIFY', $company);

        $form = $this->createFormBuilder($company)
            ->add('rejectReason', TextType::class)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->reject($company);

            $this->addFlash('notice', 'Company ' . $company->getName() . ' successfully rejected.');

            return $this->redirectToRoute('company');
        }

        return $this->render('company/reject.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }


    // Work with Review

    /**
     * @Route("/{id}/reviews/create", name="company.add.review")
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addReview(Request $request, Company $company)
    {
        $review = new Review();

        $form = $this->createForm(ReviewCreateForm::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addReview($company, $review);

            $this->addFlash('notice', 'Review successfully added.');

            return $this->redirectToRoute('company');
        }

        return $this->render('company/review/add.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/favorites/add/{id}", name="company.add.favorites")
     * @param Company $company
     * @param UserService $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToFavorites(Company $company, UserService $service)
    {
        $service->addCompanyToFavorites($company, $this->getUser());

        $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно добавлена в избранное.');

        return $this->redirectToRoute('company.show', ['id' => $company->getId()]);
    }

    /**
     * @Route("/favorites/remove/{id}", name="company.remove.favorites")
     * @param Company $company
     * @param UserService $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromFavorites(Company $company, UserService $service)
    {
        $service->removeCompanyFromFavorites($company, $this->getUser());

        $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно удалена из избранных.');

        return $this->redirectToRoute('company.show', ['id' => $company->getId()]);
    }


    /**
     * @Route("/business/attach/{id}", name="company.business.attach")
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestBusiness(Request $request, Company $company)
    {
        $businessRequest = new BusinessRequest();

        $form = $this->createForm(BusinessRequestForm::class, $businessRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addRequest($businessRequest, $company, $this->getUser());

            $this->addFlash('notice', 'Компания ' . $company->getName() . ' теперь является вашей компанией.');

            return $this->redirectToRoute('company.show', ['id' => $company->getId()]);
        }

        return $this->render('company/business/request.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
        ]);
    }

    /**
     * @Route("/business/verify/{id}", name="company.business.verify")
     * @param BusinessRequest $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verifyBusiness(BusinessRequest $request)
    {
        $this->denyAccessUnlessGranted('VERIFY', $request->getCompany());

        $this->service->attachUser($request);

        $this->addFlash('notice', 'Заявка успешно одобрена.');

        return $this->redirectToRoute('admin.request');
    }

    /**
     * @Route("/business/reject/{id}", name="company.business.reject")
     * @param BusinessRequest $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rejectBusiness(BusinessRequest $request)
    {
        $this->denyAccessUnlessGranted('VERIFY', $request->getCompany());

        $this->service->rejectRequest($request);

        $this->addFlash('notice', 'Заявка успешно отклонена.');

        return $this->redirectToRoute('admin.request');
    }
}
