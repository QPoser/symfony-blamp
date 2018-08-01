<?php

namespace App\Controller;

use App\Entity\Advert\Banner;
use App\Repository\Advert\BannerRepository;
use App\Services\AdvertService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/banner/vertical/get", name="get.banner.vertical")
     * @param Request $request
     * @param AdvertService $service
     * @param BannerRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verticalBanner(Request $request, AdvertService $service, BannerRepository $repository)
    {
        $banner = $service->getRandomBanner($repository, Banner::FORMAT_VERTICAL, $this->getUser() ?: $request->server->get('REMOTE_ADDR'));

        if (!$banner) {
            throw new \DomainException('Нет доступного вертикального баннера.');
        }

        return $this->render('advert/banner/get.html.twig', compact('banner'));
    }

    /**
     * @Route("/banner/horizontal/get", name="get.banner.horizontal")
     * @param Request $request
     * @param AdvertService $service
     * @param BannerRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function horizontalBanner(Request $request, AdvertService $service, BannerRepository $repository)
    {
        $banner = $service->getRandomBanner($repository, Banner::FORMAT_HORIZONTAL, $this->getUser() ?: $request->server->get('REMOTE_ADDR'));

        if (!$banner) {
            throw new \DomainException('Нет доступного горизонтального баннера.');
        }

        return $this->render('advert/banner/horizontal.html.twig', compact('banner'));
    }
}
