<?php

namespace App\Services;

use App\Entity\Review\ReviewComment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class CommentService
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

    public function addComment(ReviewComment $parentComment, ReviewComment $comment)
    {
        $review = $parentComment->getReview();

        $comment->setStatus(ReviewComment::STATUS_WAIT);
        $comment->setParent($parentComment);
        $review->addComment($comment);

        $this->manager->merge($comment);
        $this->manager->flush();
        $this->manager->clear();
    }
}