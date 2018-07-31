<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 31.07.18
 * Time: 19:30
 */

namespace App\Controller\Company;
use App\Entity\Company\Protector;
use App\Form\Company\ProtectorForm;
use App\Services\ProtectorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyController
 * @package App\Controller
 * @Route("/protect")
 */
class ProtectorController extends Controller
{

    /**
     * @var ProtectorService
     */
    private $service;

    public function __construct(ProtectorService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/create", name="protector.create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createProtector(Request $request)
    {
        $protector = new Protector();

        $form = $this->createForm(ProtectorForm::class, $protector);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->create($protector);

            $this->addFlash('notice', 'Протектер успешно добавлен.');

            return $this->redirectToRoute('admin');
        }

        return $this->render('company/protector/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/delete/{id}", name="protector.remove")
     * @param Protector $protector
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function removeProtector(Protector $protector)
    {
        $this->service->remove($protector);

        $this->addFlash('notice', 'Протектер успешно был удален.');

        return $this->redirectToRoute('admin');
    }




}