<?php

namespace App\Controller\Company;

use App\Entity\Company\Company;
use App\Entity\Company\CouponType;
use App\Form\Company\CouponForm;
use App\Repository\Company\CouponTypeRepository;
use App\Services\CouponService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cabinet/business/companies/coupons")
 */
class CouponTypeController extends Controller
{

    /**
     * @var CouponService
     */
    private $service;

    public function __construct(CouponService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/{id}/new", name="company.coupon.create", methods="GET|POST")
     * @param Request $request
     * @param Company $company
     * @return Response
     */
    public function new(Request $request, Company $company): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $company);

        $couponType = new CouponType();
        $form = $this->createForm(CouponForm::class, $couponType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->addCouponType($couponType, $company);

            $this->addFlash('notice', 'Новый тип купона вашей компании успешно создан, и находится на модерации.');

            return $this->redirectToRoute('cabinet.business.profile');
        }

        return $this->render('company/coupon_type/new.html.twig', [
            'coupon_type' => $couponType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/close/{id}", name="company.coupon.close")
     * @param CouponType $couponType
     * @return Response
     */
    public function close(CouponType $couponType): Response
    {
        $this->denyAccessUnlessGranted('OPEN', $couponType);

        try {
            $this->service->closeCoupon($couponType);

            $this->addFlash('notice', 'Купон успешно закрыт.');
        } catch (\DomainException $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('cabinet.business.profile');
    }

    /**
     * @Route("/open/{id}", name="company.coupon.open")
     * @param CouponType $couponType
     * @return Response
     */
    public function open(CouponType $couponType): Response
    {
        $this->denyAccessUnlessGranted('OPEN', $couponType);

        try {
            $this->service->openCoupon($couponType);

            $this->addFlash('notice', 'Купон успешно открыт.');
        } catch (\DomainException $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('cabinet.business.profile');
    }

    /**
     * @Route("/verify/{id}", name="company.coupon.verify")
     * @param CouponType $couponType
     * @return Response
     */
    public function verify(CouponType $couponType): Response
    {
        $this->denyAccessUnlessGranted('VERIFY', $couponType);

        $this->service->verifyCoupon($couponType);

        $this->addFlash('notice', 'Купон успешно проверифицирован.');

        return $this->redirectToRoute('admin.coupons');
    }

    /**
     * @Route("/reject/{id}", name="company.coupon.reject")
     * @param CouponType $couponType
     * @return Response
     */
    public function reject(CouponType $couponType): Response
    {
        $this->denyAccessUnlessGranted('VERIFY', $couponType);

        $this->service->rejectCoupon($couponType);

        $this->addFlash('notice', 'Купон успешно проверифицирован.');

        return $this->redirectToRoute('admin.coupons');
    }
}
