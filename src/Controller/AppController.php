<?php

namespace App\Controller;

use App\Repository\Advert\BannerRepository;
use App\Services\AdvertService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->redirectToRoute('company');
    }

    /**
     * @Route("/banner/get", name="get.banner")
     * @param AdvertService $service
     * @param BannerRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function banner(AdvertService $service, BannerRepository $repository)
    {
        $banner = $service->getRandomBanner($repository);

        return $this->render('advert/banner/get.html.twig', compact('banner'));
    }
}
