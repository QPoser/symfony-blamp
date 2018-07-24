<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;

use App\Entity\Like;
use App\Entity\Review;
use App\Entity\ReviewComment;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DependencyInjection\Container;

class ReviewService
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
        $TEM = new TreeEntityManager();
        $this->manager = $TEM->getTEM($manager);
        $this->container = $container;
    }


    public function addComment(Review $review, ReviewComment $comment)
    {
        $comment->setStatus(Review::STATUS_WAIT);
        $review->addComment($comment);

        $this->manager->merge($comment);
        $this->manager->flush();
        $this->manager->clear();
    }

    public function edit(Review $review)
    {
        //
        $this->manager->flush();
        $review->getCompany()->calcAssessment();
        $this->manager->flush();
    }

    public function delete(Review $review)
    {
        $this->manager->remove($review);
        $this->manager->flush();
        $review->getCompany()->calcAssessment();
        $this->manager->flush();
    }

    public function addLike(Review $review, User $user, bool $value = Like::LIKE)
    {
        $like = new Like();
        $like->setUser($user);
        $like->setValue($value);
        $review->addLike($like);

        $this->manager->persist($like);
        $this->manager->persist($review);
        $this->manager->flush();
        $review->likesCount();
        $this->manager->flush();
    }

    public function removeLike(Like $like)
    {
        $review = $like->getReview();
        $this->manager->remove($like);
        $this->manager->flush();
        $review->likesCount();
        $this->manager->flush();
    }

    public function findLikeByUser(Review $review, User $user, bool $value = Like::LIKE)
    {
        foreach ($review->getLikes() as $like) {
            $liker = $like->getUser()->getId();
            if ($like->getValue() == $value) {
                if ($liker == $user->getId()) {
                    $this->removeLike($like);
                    return true;
                }
            }else {
                if ($liker == $user->getId()) {
                    $this->removeLike($like);
                    break;
                }
            }
        }
        return false;
    }
}