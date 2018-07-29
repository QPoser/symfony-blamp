<?php

namespace App\Controller\Admin;

use App\Entity\Advert\Banner;
use App\Entity\Company\CouponType;
use App\Repository\Advert\AdvertDescriptionRepository;
use App\Repository\Advert\BannerRepository;
use App\Repository\Company\BusinessRequestRepository;
use App\Repository\Company\CompanyRepository;
use App\Repository\Company\CouponTypeRepository;
use App\Repository\Review\ReviewCommentRepository;
use App\Repository\Review\ReviewRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @var \App\Repository\Company\CompanyRepository
     */
    private $companyRepository;
    /**
     * @var \App\Repository\Review\ReviewRepository
     */
    private $reviewRepository;
    /**
     * @var ReviewCommentRepository
     */
    private $commentRepository;
    /**
     * @var BusinessRequestRepository
     */
    private $requestRepository;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var CouponTypeRepository
     */
    private $couponTypeRepository;

    private $counts = [];

    public function __construct(
                                CompanyRepository $companyRepository,
                                ReviewRepository $reviewRepository,
                                ReviewCommentRepository $commentRepository,
                                BusinessRequestRepository $requestRepository,
                                BannerRepository $bannerRepository,
                                AdvertDescriptionRepository $adDescriptionRepository,
                                CouponTypeRepository $couponTypeRepository
                                )
    {
        $this->companyRepository = $companyRepository;
        $this->reviewRepository = $reviewRepository;
        $this->commentRepository = $commentRepository;
        $this->requestRepository = $requestRepository;
        $this->bannerRepository = $bannerRepository;
        $this->adDescriptionRepository = $adDescriptionRepository;
        $this->couponTypeRepository = $couponTypeRepository;

        $this->counts['companies'] = $companyRepository->getCountOfNewCompanies();
        $this->counts['reviews'] = $reviewRepository->getCountOfNewReviews();
        $this->counts['requests'] = $requestRepository->getCountOfNewRequests();
        $this->counts['adverts'] = $bannerRepository->getCountOfNewBanners();
        $this->counts['adverts'] += $adDescriptionRepository->getCountOfNewAdverts();
        $this->counts['coupons'] = $couponTypeRepository->getCountOfNewCoupons();
    }

    /**
     * @Route("/", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function companies()
    {
        $companies = $this->companyRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'companies' => $companies,
            'waitCounts' => $this->counts,
        ]);
    }

    /**
     * @Route("/reviews", name="admin.review")
     */
    public function reviews()
    {
        $reviews = $this->reviewRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/reviews.html.twig', [
            'reviews' => $reviews,
            'waitCounts' => $this->counts,
        ]);
    }

    /**
     * @Route("/requests", name="admin.request")
     */
    public function requests()
    {
        $requests = $this->requestRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/requests.html.twig', [
           'requests' => $requests,
           'waitCounts' => $this->counts,
        ]);
    }

    /**
     * @Route("/adverts", name="admin.adverts")
     */
    public function adverts()
    {
        $banners = $this->bannerRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/adverts.html.twig', [
           'banners' => $banners,
           'waitCounts' => $this->counts,
        ]);
    }

    /**
     * @Route("/coupons", name="admin.coupons")
     */
    public function coupons()
    {
        $coupons = $this->couponTypeRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/coupons.html.twig', [
            'coupons' => $coupons,
            'waitCounts' => $this->counts,
        ]);
    }

    /**
     * @Route("/adverts/descriptions", name="admin.adverts.descriptions")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function advertDescriptions()
    {
        $descriptions = $this->adDescriptionRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/ad_descriptions.html.twig', [
            'descriptions' => $descriptions,
            'waitCounts' => $this->counts,
        ]);
    }
}
