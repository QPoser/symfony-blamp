<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;


use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
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
     * @param Review $review
     * @param ReviewComment $comment
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(Review $review, ReviewComment $comment)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $user = $this->storage->getToken()->getUser();

        $comment->setUser($user);
        $review->addComment($comment);

        $this->manager->persist($comment);
        $this->manager->flush();

        if ($comment->getIsCompany()) {
            $return = $this->eventService->addEventByCompany(
                $review->getUser(),
                'Добавил комментарий к вашему отзыву на компанию <a href="' .
                $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $review->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">' . $review->getCompany()->getName() . '</a>',
                $review->getCompany()
            );
        } else {
            $return = $this->eventService->addEventByUser(
                $review->getUser(),
                'Добавил комментарий к вашему отзыву на компанию <a href="' .
                $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $review->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                '">' . $review->getCompany()->getName() . '</a>',
                $comment->getUser()
            );
        }


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