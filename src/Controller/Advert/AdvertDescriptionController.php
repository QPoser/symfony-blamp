<?php

namespace App\Controller\Advert;

use App\Entity\Advert\AdvertDescription;
use App\Entity\Advert\Banner;
use App\Entity\Company\Company;
use App\Form\Advert\AdvertDescriptionType;
use App\Form\Advert\BannerType;
use App\Repository\Advert\BannerRepository;
use App\Services\AdvertService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cabinet/business/adverts/descriptions")
 */
class AdvertDescriptionController extends Controller
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
     * @Route("/create/{id}", name="advert.description.create", methods="GET|POST")
     * @param Request $request
     * @param Company $company
     * @return Response
     */
    public function create(Request $request, Company $company): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $company);

        $description = new AdvertDescription();
        $form = $this->createForm(AdvertDescriptionType::class, $description);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addDescription($description, $company);

            $this->addFlash('notice', 'Ваша заявка на новый описание компании успешно добавлена!');

            return $this->redirectToRoute('cabinet.business.adverts');
        }

        return $this->render('advert/description/new.html.twig', [
            'description' => $description,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pay/{id}", name="advert.description.pay", methods="GET")
     * @param AdvertDescription $description
     * @return Response
     */
    public function pay(AdvertDescription $description): Response
    {
        $this->denyAccessUnlessGranted('PAY', $description);

        $this->service->payDescription($description);

        $this->addFlash('notice', 'Вы успешно оплатили динамическое описание для компании '
             . $description->getCompany()->getName());

        return $this->redirectToRoute('cabinet.business.adverts');
    }

    /**
     * @Route("/verify/{id}", name="advert.description.verify")
     * @param AdvertDescription $description
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function verify(AdvertDescription $description)
    {
        $this->denyAccessUnlessGranted('VERIFY', $description);

        $this->service->verifyDescription($description);

        $this->addFlash('notice', 'Описание было верифицировано, и готово к оплате для пользователя');

        return $this->redirectToRoute('admin.adverts.descriptions');
    }

    /**
     * @Route("/reject/{id}", name="advert.description.reject")
     * @param AdvertDescription $description
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reject(AdvertDescription $description)
    {
        $this->denyAccessUnlessGranted('VERIFY', $description);

        $this->service->rejectDescription($description);

        $this->addFlash('notice', 'Описание было успешно отклонено!');

        return $this->redirectToRoute('admin.adverts.descriptions');
    }
}
