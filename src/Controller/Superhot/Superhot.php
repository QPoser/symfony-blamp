<?php


namespace App\Controller\Superhot;

use App\Repository\Company\CompanyRepository;
use App\Repository\Review\ReviewRepository;
use App\Repository\User\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/superhot")
 */
class Superhot extends Controller
{
    /**
     * @var CompanyRepository
     */
    private $companyRepo;

    private $userRepo;

    private $reviewRepo;

    public function __construct(CompanyRepository $companyRepo, UserRepository $userRepo, ReviewRepository $reviewRepo)
    {
        $this->companyRepo = $companyRepo;
        $this->userRepo = $userRepo;
        $this->reviewRepo = $reviewRepo;
    }

    public function index()
    {
        //$companies = $this->companyRepo->findAll()









        return $this->render('company/index.html.twig', compact('companies','users', 'reviews'));
    }
}