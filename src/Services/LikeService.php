<?php

namespace App\Services;


use App\Entity\Review\Like;
use App\Entity\Review\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LikeService
{
    /**
     * @var array
     */
    private $statement;
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
        $this->statement = array(
            'like' => 'Понравился',
            'dislike' => 'Не понравился',
            'erase' => 'Убрал',
        );
    }


    /**
     * @param Review $review
     * @param bool $value
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addLike(Review $review, bool $value = Like::LIKE)
    {
        $user = $this->storage->getToken()->getUser();
        if ($value == Like::LIKE) {
            $eventStatement  = $this->statement['like'];
        }else $eventStatement  = $this->statement['dislike'];

        foreach ($review->getLikes() as $like) {
            $liker = $like->getUser();
            if ($liker == $user) {
                if ($like->getValue() == $value) {
                    $eventStatement  = $this->statement['erase'];
                    $this->notify($review, $like, $eventStatement);
                    $this->delete($like);
                    $review->likesCount();
                    $this->manager->flush();
                    return false;
                } else {
                    $this->delete($like);
                    $newLike = $this->createLike($review, $user, $value);
                    $this->notify($review, $newLike, $eventStatement);
                    return true;
                }
            }
        }
        $newLike = $this->createLike($review, $user, $value);
        $this->notify($review, $newLike, $eventStatement);
        return true;
    }

    public function delete(Like $like)
    {
        $this->manager->remove($like);
        $this->manager->flush();
    }

    public function createLike(Review $review, User $user, $value)
    {
        $newLike = new Like();
        $newLike->setValue($value);
        $newLike->setUser($user);
        $review->addLike($newLike);
        $this->manager->persist($newLike);
        $this->manager->flush();
        $review->likesCount();
        $this->manager->flush();
        return $newLike;
    }

    public function notify(Review $review, Like $like, string $statement)
    {
        switch ($statement) {
            case $this->statement['erase']:
                    $return = $this->eventService->addEventByUser(
                        $review->getUser(),
                        $statement . ' оценку вашего отзыва на компанию <a href="' .
                        $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $review->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                        '">' . $review->getCompany()->getName() . '</a>',
                        $like->getUser()
                    );
                break;
            default:
                $return = $this->eventService->addEventByUser(
                    $review->getUser(),
                    $statement . ' ваш отзыв на компанию <a href="' .
                    $this->container->get('router')->getGenerator()->generate('company.show', ['id' => $review->getCompany()->getId()], UrlGeneratorInterface::ABSOLUTE_URL) .
                    '">' . $review->getCompany()->getName() . '</a>',
                    $like->getUser()
                );
                break;
        }
    }

    public function addLikeFixtureMod(User $user, Review $review, $value)
    {
        if ($value == Like::LIKE) {
            $eventStatement  = $this->statement['like'];
        }else $eventStatement  = $this->statement['dislike'];

        foreach ($review->getLikes() as $like) {
            $liker = $like->getUser();
            if ($liker == $user) {
                if ($like->getValue() == $value) {
                    $eventStatement  = $this->statement['erase'];
                    $this->notify($review, $like, $eventStatement);
                    $this->delete($like);
                    $review->likesCount();
                    $this->manager->flush();
                    return;
                } else {
                    $this->delete($like);
                    $newLike = $this->createLike($review, $user, $value);
                    $this->notify($review, $newLike, $eventStatement);
                    return;
                }
            }
        }
        $newLike = $this->createLike($review, $user, $value);
        $this->notify($review, $newLike, $eventStatement);
        return;
    }
}