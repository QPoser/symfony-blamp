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
        $this->manager = $manager;
        $this->container = $container;
    }


    public function addComment(Review $review, ReviewComment $comment)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $review->addComment($comment);

        $this->manager->persist($comment);
        $this->manager->flush();
    }

}