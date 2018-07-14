<?php

namespace App\Controller;

use App\Entity\Company\Company;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyController extends Controller
{
    /**
     * @Route("/company", name="company")
     */
    public function index()
    {
        $companies = $this->getDoctrine()
            ->getRepository('App:Company\Company')
            ->findAll();

        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
            'companies' => $companies,
        ]);
    }
}
