<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;


use App\Entity\Company\BusinessRequest;
use App\Entity\Company\Company;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Services\App\EmailService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CompanyService
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var EmailService
     */
    private $emailService;
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * CompanyService constructor.
     * @param EntityManager $manager
     * @param Container $container
     * @param EmailService $emailService
     * @param EventService $eventService
     */
    public function __construct(EntityManager $manager, Container $container, EmailService $emailService, EventService $eventService)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->emailService = $emailService;
        $this->eventService = $eventService;
    }


    public function create(Company $company, Form $form = null, $email = null): Company
    {

        if ($form && $form['photo']) {
            $file = $form['photo']->getData();
            $this->setPhoto($file, $company);
        }

        if (!$company->getCreatorEmail() && $email) {
            $company->setCreatorEmail($email);
        }

        $company->setStatus(Company::STATUS_WAIT);
        $this->manager->persist($company);
        $this->manager->flush();
        return $company;
    }

    public function edit(Company $company, Form $form): Company
    {
        if ($form['photo']) {
            $file = $form['photo']->getData();
            $this->setPhoto($file, $company);
        }

        $this->manager->flush();
        return $company;
    }

    public function remove(Company $company)
    {
        $this->manager->remove($company);
        $this->manager->flush();
    }

    public function verify(Company $company)
    {
        $company->setStatus(Company::STATUS_ACTIVE);
        if ($company->getCreatorEmail()) {
            $this->emailService->sendSimpleMessage('Компания ' . $company->getName() . ' успешно принята!',
                'Компания ' . $company->getName() . ' которую вы добавили ранее, была успешно принята на сайт, и доступна для просмотра!',
                $company->getCreatorEmail());
        }

        $this->manager->flush($company);
    }

    public function reject(Company $company)
    {
        $company->setStatus(Company::STATUS_REJECTED);

        if ($company->getCreatorEmail()) {
            $this->emailService->sendSimpleMessage('Компания ' . $company->getName() . ' была отклонена!',
                'Компания ' . $company->getName() . ' которую вы добавили ранее, была отклонена по причине: ' . $company->getRejectReason(),
                $company->getCreatorEmail());
        }

        $this->manager->flush($company);
    }

    public function addReview(Company $company, Review $review, User $user)
    {
        $review->setCompany($company);
        $review->setStatus(Review::STATUS_WAIT);
        $review->setUser($user);

        $this->manager->persist($review);
        $this->manager->flush();
        $company->calcAssessment();
        $this->manager->flush();
    }

    // Business

    public function addRequest(BusinessRequest $request, Company $company, User $user)
    {
        $request->setCompany($company);
        $request->setUser($user);
        $request->setStatus(BusinessRequest::STATUS_WAIT);
        $this->manager->persist($request);
        $this->manager->flush();
    }

    public function attachUser(BusinessRequest $request)
    {
        $company = $request->getCompany();
        $user = $request->getUser();
        $request->setStatus(BusinessRequest::STATUS_SUCCESS);
        $company->addBusinessUser($user);

        $this->eventService->addEventByCompany($user, 'Вы успешно стали владельцем компании', $company);

        $this->manager->flush();
    }

    public function rejectRequest(BusinessRequest $request)
    {
        $request->setStatus(BusinessRequest::STATUS_REJECTED);

        $this->deattachUser($request->getCompany(), $request->getUser());

        $this->eventService->addEventByCompany($request->getUser(), 'Вам отказано в привязке компании', $request->getCompany());

        $this->manager->flush();
    }

    public function deattachUser(Company $company, User $user)
    {
        $company->removeBusinessUser($user);

        $this->eventService->addEventByCompany($user, 'Вы были отвязаны от компании, и больше не являетесь её владельцем', $company);

        $this->manager->flush();
    }


    // Set photo
    private function setPhoto(?UploadedFile $file, Company $company)
    {
        $package = new Package(new EmptyVersionStrategy());
        if ($file) {
            $someFileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->container->getParameter('img_dir'), $someFileName);
            $company->setPhoto($package->getUrl('uploads/img/' . $someFileName));
        }
    }

}