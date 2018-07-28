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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * @var TokenStorage
     */
    private $storage;

    /**
     * CompanyService constructor.
     * @param EntityManager $manager
     * @param Container $container
     */
    public function __construct(EntityManager $manager, Container $container)
    {
        $this->manager = $manager;
        $this->container = $container;
    }


    public function create(Company $company, Form $form): Company
    {

        if ($form['photo']) {
            $file = $form['photo']->getData();
            $this->setPhoto($file, $company);
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
        $this->manager->flush($company);
    }

    public function reject(Company $company)
    {
        $company->setStatus(Company::STATUS_REJECTED);
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

        $this->manager->flush();
    }

    public function rejectRequest(BusinessRequest $request)
    {
        $request->setStatus(BusinessRequest::STATUS_REJECTED);

        $this->deattachUser($request->getCompany(), $request->getUser());

        $this->manager->flush();
    }

    public function deattachUser(Company $company, User $user)
    {
        $company->removeBusinessUser($user);

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