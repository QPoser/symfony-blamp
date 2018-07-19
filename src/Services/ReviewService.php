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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(EntityManager $manager, Container $container, TokenStorage $storage)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->storage = $storage;
    }


    public function addComment(Review $review, ReviewComment $comment)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $comment->setUser($this->storage->getToken()->getUser());
        $review->addComment($comment);

        $this->manager->persist($comment);
        $this->manager->flush();
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