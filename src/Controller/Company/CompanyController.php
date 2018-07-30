<?php

namespace App\Controller\Company;

use App\Entity\Category\Category;
use App\Entity\Company\BusinessRequest;
use App\Entity\Company\Company;
use App\Entity\Event;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Form\Category\CategoryType;
use App\Form\Company\BusinessRequestForm;
use App\Form\Company\CompanyCreateForm;
use App\Form\Company\CompanyEditForm;
use App\Form\Company\Review\ReviewCreateForm;
use App\Repository\Company\CompanyRepository;
use App\Repository\Company\CouponTypeRepository;
use App\Services\CompanyService;
use App\Services\EventService;
use App\Services\ReviewService;
use App\Services\UserService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @var EventService
     */
    private $eventService;
    /**
     * @var CompanyRepository
     */
    private $repository;

    public function __construct(CompanyService $service, EventService $eventService, CompanyRepository $repository)
    {
        $this->service = $service;
        $this->eventService = $eventService;
        $this->repository = $repository;
    }

    /**
     * @Route("", name="company")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $companies = $this->repository->search($request->get('search'), $request->get('page') ?: 1);

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($companies->count() / 4);

        return $this->render('company/index.html.twig', compact('companies', 'maxPages', 'thisPage'));
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

        $email = null;

        if (!$this->getUser() || !$this->getUser()->getEmail()) {
            $form->add('creatorEmail', EmailType::class, [
                'required' => false,
                'label' => 'Email для уведомлений'
            ]);
        } else {
            $email = $this->getUser()->getEmail();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $company = $this->service->create($company, $form, $email);

            $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно добавлена.');

            return $this->redirectToRoute('company');
        }

        return $this->render('company/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="company.show")
     * @param Company $company
     * @param CouponTypeRepository $couponRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Company $company, CouponTypeRepository $couponRepository)
    {
        $this->denyAccessUnlessGranted('SHOW', $company);

        $coupons = $couponRepository->findActiveByCompany($company);

        return $this->render('company/show.html.twig', compact('company', 'coupons'));
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

        $company->removeAllCategories();

        $form = $this->createForm(CompanyEditForm::class, $company);

        if (in_array(User::ROLE_ADMIN, $this->getUser()->getRoles())) {
            $form->add('categories', EntityType::class, [
                'class' => 'App\Entity\Category\Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.num', 'ASC');
                },
                'choice_label' => 'path',
                'multiple' => true,
                'required' => false,
                'choice_attr' => function($choiceValue, $key, $value) {
                    if ($this->getDoctrine()->getRepository('App:Category\Category')->findOneBy(['id' => $value])->getChildrenCategories()->getValues())
                        return ['disabled' => 'disabled'];
                    return [];
                    },

            ])
//                ->add('tags', EntityType::class, [
//                    'class' => 'App\Entity\Company\Tag',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('u')
//                            ->orderBy('u.num', 'ASC');
//                    },
//                    'choice_label' => 'path',
//                    'multiple' => true,
//                    'required' => false,
//                    'choice_attr' => function($choiceValue, $key, $value) {
//                        if ($this->getDoctrine()->getRepository('App:Category\Category')->findOneBy(['id' => $value])->getChildrenCategories()->getValues())
//                            return ['disabled' => 'disabled'];
//                        return [];
//                    },
//
//                ])
            ;

        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $this->service->edit($company, $form);

            $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно изменена.');

            return $this->redirectToRoute('company.show', ['id' => $company->getId()]);
        }

        return $this->render('company/edit.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
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

        $events = $this->getDoctrine()->getRepository(Event::class)->findBy(['senderCompany' => $company]);
        foreach ($events as $event)
            $this->eventService->removeEvent($event);

        $this->service->remove($company);

        $this->addFlash('warning', 'Компания ' . $company->getName() . ' успешно удалена.');

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

        $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно принята.');

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

            $this->addFlash('notice', 'Компания ' . $company->getName() . ' успешно отклонена.');

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
     * @param ReviewService $reviewService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addReview(Request $request, Company $company, ReviewService $reviewService)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = new Review();

        $form = $this->createForm(ReviewCreateForm::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addReview($company, $review, $this->getUser());

            if ($this->getUser()->isAdmin()) {
                $reviewService->verify($review);
            }

            $this->addFlash('notice', 'Отзыв успешно добавлен.');

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
        $this->denyAccessUnlessGranted('BUSINESS', $company);

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
