<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;


use App\Entity\Review;
use App\Entity\ReviewComment;
use Doctrine\ORM\EntityManager;
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

}