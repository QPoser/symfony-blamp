<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;


use App\Entity\Company\Company;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function addReview(Company $company, Review $review)
    {
        $review->setCompany($company);
        $review->setStatus(Review::STATUS_WAIT);


        $this->manager->persist($review);
        $this->manager->flush();
        $company->calcAssessment();
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