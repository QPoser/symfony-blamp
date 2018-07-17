<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Time;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <=10; $i++) {
            $company = new Company();
            $company->setName('Company '.$i);
            $company->setPhone('+79876543210');
            $company->setStartWork(new Time("09:00"));
            $company->setEndWork(new Time("18:00"));
            $company->setSite('https://www.google'.$i.'.ru');
            $company->setPhoto('public/source/img/company-logo.png');
            $company->setStatus(Company::STATUS_ACTIVE);
            $company->setAssessment(5.0);


            $manager->persist($company);
            $manager->flush();
        }

    }

    public function getDependencies()
    {
        return array(
            Fixtures::class,
        );
    }
}
