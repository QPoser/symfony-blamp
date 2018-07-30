<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 14.07.18
 * Time: 13:20
 */

namespace App\Services;


use App\Entity\Company\Coupon;
use App\Entity\Review\Review;
use App\Entity\Review\ReviewComment;
use App\Entity\User;
use App\Repository\Company\CouponRepository;
use App\Repository\Company\CouponTypeRepository;
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
     * @var CouponTypeRepository
     */
    private $couponRepository;
    /**
     * @var CouponRepository
     */
    private $coupons;

    /**
     * ReviewService constructor.
     * @param EntityManager $manager
     * @param Container $container
     * @param EventService $eventService
     * @param CouponTypeRepository $couponRepository
     * @param CouponRepository $coupons
     */
    public function __construct(EntityManager $manager, Container $container, EventService $eventService, CouponTypeRepository $couponRepository, CouponRepository $coupons)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->eventService = $eventService;
        $this->couponRepository = $couponRepository;
        $this->coupons = $coupons;
    }


    /**
     * @param Review $review
     * @param ReviewComment $comment
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(Review $review, ReviewComment $comment, User $user)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $comment->setUser($user);

        if (!$comment->getIsCompany()) {
            $comment->setIsCompany(false);
        }

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

    public function verify(Review $review)
    {
        $review->setStatus(Review::STATUS_ACTIVE);

        $this->eventService->addEventByCompany($review->getUser(),
            'Ваш отзыв для компании ' . $review->getCompany()->getName() . ' был успешно одобрен, и опубликован!',
            $review->getCompany());

        if (!empty($review->getCompany()->getCouponTypes())) {
            $couponType = $this->couponRepository->getRandomActiveCouponByCompany($review->getCompany());

            $coupon = new Coupon();
            $coupon->setCompany($review->getCompany());
            $coupon->setCouponType($couponType);
            $coupon->setUser($review->getUser());
            $coupon->setCode($this->generateCouponCode($review->getUser()));
            $coupon->setActive();

            $this->eventService->addEventByCompany($review->getUser(),
                'Вы получили новый купон от компании ' . $review->getCompany()->getName(),
                $review->getCompany());

            $this->manager->persist($coupon);
        }

        $review->getCompany()->calcAssessment();

        $this->manager->flush();
    }

    public function reject(Review $review)
    {
        $review->setStatus(Review::STATUS_REJECTED);

        $review->getCompany()->calcAssessment();

        $this->manager->flush();
    }

    public function addCommentFixtureMod(Review $review, ReviewComment $comment, User $user)
    {
        $comment->setStatus(Review::STATUS_WAIT);

        $comment->setUser($user);

        if (!$comment->getIsCompany()) {
            $comment->setIsCompany(false);
        }

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
    private function generateCouponCode(User $user)
    {
        $minVal = 100000;
        $maxVal = 999999;

        $code = $user->getId() . substr(rand($minVal, $maxVal), 0, 6 - mb_strlen($user->getId()));

        if (!$this->coupons->findOneBy(['code' => $code])) {
            return $code;
        }

        return $this->generateCouponCode();
    }

}