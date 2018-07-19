<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $companyRepo = $manager->getRepository(Company::class);
        $companies = $companyRepo->findAll();
        for ($i = 0; $i < count($companies); $i++) {
            for ($j = 0; $j <= 5; $j++) {
                $review = new Review();
                $review->setText('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid corporis dolores enim, esse et ex harum illum incidunt ipsum, maiores nisi nulla numquam officiis perferendis quidem sint tempore ut voluptas.');
                $review->setAssessment(mt_rand(1, 5));
                $review->setStatus(Review::STATUS_ACTIVE);


                $company = $companies[array_rand($companies)];
                if ($company)
                    $review->setCompany($company);

                $users = $userRepo->findAll();
                $user = $users[array_rand($users)];
                if ($user)
                    $review->setUser($user);

                $manager->persist($review);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            CompanyFixtures::class,
        );
    }
}
