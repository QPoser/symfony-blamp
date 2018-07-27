<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\Review\Like;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Services\LikeService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Time;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var LikeService
     */
    private $service;

    public function __construct(LikeService $likeService)
    {
        $this->service = $likeService;
    }

    public function load(ObjectManager $manager)
    {
        $reviewRepo = $manager->getRepository(Review::class);
        $reviews = $reviewRepo->findAll();
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();
        for ($j = 0; $j < ((count($reviews)+count($users))*4); $j++) {
            $review = $reviews[array_rand($reviews)];
            $user = $users[array_rand($users)];
            $values = [Like::LIKE, Like::DISLIKE];
            $value = $values[array_rand($values)];
            $this->service->addLikeFixtureMod($user, $review, $value);
        }
    }

    public function getDependencies()
    {
        return array(
            ReviewFixtures::class,

        );
    }
}