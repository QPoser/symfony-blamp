<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\Like;
use App\Entity\Review;
use App\Entity\User;
use App\Services\ReviewService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Time;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function load(ObjectManager $manager)
    {
        $reviewRepo = $manager->getRepository(Review::class);
        $reviews = $reviewRepo->findAll();
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();
        $i = 1;
        foreach ($users as $user) {
            if (($i%4) == 0){
                $i++;
                continue;
            }
            for ($j = 0; $j <10; $j++) {
                $review = $reviews[array_rand($reviews)];
                $values = [Like::LIKE, Like::DISLIKE];
                $value = $values[array_rand($values)];
                $this->service->addLike($review, $user, $value);
            }
            $i++;
        }
    }

    public function getDependencies()
    {
        return array(
            ReviewFixtures::class,
        );
    }
}