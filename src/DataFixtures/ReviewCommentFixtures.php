<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\ReviewComment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ReviewCommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        $reviewRepo = $manager->getRepository(Review::class);
//        $reviews = $reviewRepo->findAll();
//        $userRepo = $manager->getRepository(User::class);
//        $users = $userRepo->findAll();
//        $i = 1;
//        foreach ($reviews as $review) {
//            if (($i%4) == 0){
//                $i++;
//                continue;
//            }
//            $c = mt_rand(0,4);
//            for ($c; $c < 5; $c++) {
//                $user = $users[array_rand($users)];
//                $comment = new ReviewComment();
//                $comment->setReview($review);
//                $comment->setUser($user);
//                $comment->setIsCompany(true);
//                $comment->setText('Nulla lacus enim, vestibulum sed augue a, lobortis finibus leo. Fusce ornare sagittis ligula, nec fringilla eros sollicitudin in. Aliquam lacinia lorem lacus, et ullamcorper lectus pulvinar ut. In sagittis elit diam.');
//                $comment->setStatus(ReviewComment::STATUS_ACTIVE);
//
//                for ($t = 0; $t < 3; $t++) {
//                    $comment2 = new ReviewComment();
//                    $comment2->setParent()
//                }
//                $c++;
//            }
//            $i++;
//        }
//        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ReviewFixtures::class
        );
    }
}
