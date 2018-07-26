<?php

namespace App\Controller\Admin;

use App\Entity\Advert\Banner;
use App\Repository\Advert\BannerRepository;
use App\Repository\Company\BusinessRequestRepository;
use App\Repository\CompanyRepository;
use App\Repository\ReviewCommentRepository;
use App\Repository\ReviewRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var ReviewRepository
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

    private $counts = [];

    public function __construct(
                                CompanyRepository $companyRepository,
                                ReviewRepository $reviewRepository,
                                ReviewCommentRepository $commentRepository,
                                BusinessRequestRepository $requestRepository,
                                BannerRepository $bannerRepository
                                )
    {
        $this->companyRepository = $companyRepository;
        $this->reviewRepository = $reviewRepository;
        $this->commentRepository = $commentRepository;
        $this->requestRepository = $requestRepository;
        $this->bannerRepository = $bannerRepository;

        $this->counts['companies'] = count($companyRepository->getWaitCompanies());
        $this->counts['reviews'] = count($reviewRepository->getWaitReviews());
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
     * @Route("/review/", name="admin.review")
     */
    public function reviews()
    {
        $reviews = $this->reviewRepository->findBy([], ['status' => 'DESC']);

        return $this->render('admin/reviews.html.twig', [
            'reviews' => $reviews,
            'waitCounts' => $this->counts,
        ]);
    }
}
