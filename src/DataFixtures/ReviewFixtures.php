<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Time;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $companyRepo = $manager->getRepository(Company::class);
        for ($i = 0; $i <= 10; $i++) {
            for ($j = 0; $j <= 15; $j++) {
                $review = new Review();
                $review->setText('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid corporis dolores enim, esse et ex harum illum incidunt ipsum, maiores nisi nulla numquam officiis perferendis quidem sint tempore ut voluptas.');
//                $review->set
//                $categories = $categoryRepo->findAll();
//                $category = $categories[array_rand($categories)];
//                if ($category) {
//                    $product->setCategory($category);
//                }
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
