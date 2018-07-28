<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Time;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <=10; $i++) {
            $company = new Company();
            $company->setName('Company '.$i);
            $company->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab ad aliquam animi assumenda consequatur culpa dolores eius enim facilis inventore ipsa magnam nemo nesciunt odio pariatur qui, ratione sapiente suscipit?');
            $company->setPhone('+79876543210');
            $company->setStartWork(new \DateTime("09:00"));
            $company->setEndWork(new \DateTime("18:00"));
            $company->setSite('https://www.google'.$i.'.ru');
            //$company->setPhoto('public/source/img/company-logo.png');
            $company->setStatus(Company::STATUS_ACTIVE);

            $manager->persist($company);
        }
        $manager->flush();

    }
}
