<?php

namespace App\Services;


use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
    /**
     * @var TokenStorage
     */
    private $storage;
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * ReviewService constructor.
     * @param EntityManager $manager
     * @param Container $container
     * @param TokenStorage $storage
     * @param EventService $eventService
     */
    public function __construct(EntityManager $manager, Container $container, TokenStorage $storage, EventService $eventService)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->storage = $storage;
        $this->eventService = $eventService;
    }


    /**
     * @param ReviewComment $reviewComment
     * @param ReviewComment $comment
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(ReviewComment $reviewComment, ReviewComment $comment)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $user = $this->storage->getToken()->getUser();

        $comment->setUser($user);

        if (!$comment->getIsCompany()) {
            $comment->setIsCompany(false);
        }

        $reviewComment->addChildrenComment($comment);

        $this->manager->persist($comment);
        $this->manager->flush();

        if ($comment->getIsCompany()) {
            $return = $this->eventService->addEventByCompany(
                $reviewComment->getUser(),
                'Ответил на ваш комментарий к отзыву на компанию <a href="' .
                $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $reviewComment->getReview()->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">' . $reviewComment->getReview()->getCompany()->getName() . '</a> (<a href="' .
                $this->container->get('router')->getGenerator()->generate('review.show', ['id' => $reviewComment->getReview()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">смотреть</a>)',
                $reviewComment->getReview()->getCompany()
            );
        } else {
            $return = $this->eventService->addEventByUser(
                $reviewComment->getUser(),
                'Ответил на ваш комментарий к отзыву на компанию <a href="' .
                $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $reviewComment->getReview()->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">' . $reviewComment->getReview()->getCompany()->getName() . '</a> (<a href="' .
                $this->container->get('router')->getGenerator()->generate('review.show', ['id' => $reviewComment->getReview()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">смотреть</a>)',
                $comment->getUser()
            );
        }


    }

    public function edit(ReviewComment $comment)
    {
        $comment->setUpdatedAt();
        $this->manager->merge($comment);
        $this->manager->flush();
    }

    public function delete(ReviewComment $comment)
    {
        $this->manager->remove($comment);
        $this->manager->flush();

    }

}