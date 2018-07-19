<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CabinetController extends Controller
{
    /**
     * @Route("/cabinet", name="cabinet")
     */
    public function index()
    {
        return $this->render('cabinet/index.html.twig', [
            'controller_name' => 'CabinetController',
        ]);
    }
}
