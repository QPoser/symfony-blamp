<?php

namespace App\Controller\Advert;

use App\Entity\Advert\Banner;
use App\Form\Advert\BannerType;
use App\Repository\Advert\BannerRepository;
use App\Services\AdvertService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cabinet/business/adverts")
 */
class BannerController extends Controller
{

    /**
     * @var AdvertService
     */
    private $service;

    public function __construct(AdvertService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/create", name="advert.banner.create", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $banner = new Banner();
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->requestBanner($banner, $this->getUser(), $form['bannerImg']->getData());

            $this->addFlash('notice', 'Ваша заявка на новый баннер успешно добавлена!');

            return $this->redirectToRoute('cabinet.business.adverts');
        }

        return $this->render('advert/banner/new.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pay/{id}", name="advert.banner.pay", methods="GET")
     * @param Banner $banner
     * @return Response
     */
    public function pay(Banner $banner): Response
    {
        $this->service->pay($banner);

        $this->addFlash('notice', 'Вы успешно оплатили данный баннер на тысячу показов');

        return $this->redirectToRoute('cabinet.business.adverts');
    }

    /**
     * @Route("/{id}/edit", name="advert_banner_edit", methods="GET|POST")
     * @param Request $request
     * @param Banner $banner
     * @return Response
     */
    public function edit(Request $request, Banner $banner): Response
    {
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_banner_edit', ['id' => $banner->getId()]);
        }

        return $this->render('advert_banner/edit.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }
}
