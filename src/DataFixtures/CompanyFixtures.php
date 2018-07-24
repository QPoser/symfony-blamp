<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <=10; $i++) {
            $company = new Company();
            $company->setName('Company '.$i);
            $company->setPhone('+79876543210');
            $company->setStartWork(new \DateTime("09:00"));
            $company->setEndWork(new \DateTime("18:00"));
            $company->setSite('https://www.google'.$i.'.ru');
            $company->setPhoto('public/uploads/company-logo.png');
            $company->setStatus(Company::STATUS_ACTIVE);

            $manager->persist($company);
        }
        $manager->flush();

    }
}
