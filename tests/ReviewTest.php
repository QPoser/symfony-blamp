<?php

namespace App\Tests;

use App\Entity\Company\Company;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Services\CompanyService;
use App\Services\ReviewService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReviewTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ReviewService
     */
    private $reviewService;

    /**
     * @var CompanyService
     */
    private $companyService;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->reviewService = $kernel->getContainer()->get(ReviewService::class);
        $this->companyService = $kernel->getContainer()->get(CompanyService::class);
    }

    public function testCreate()
    {
        $company = new Company();
        $company->setPhone('+79997778855');
        $company->setName('Test Company 2');

        $this->companyService->create($company);

        $review = new Review();
        $review->setText('Good Review');
        $review->setAssessment(4);

        $user = new User();
        $user->setUsername('User');
        $user->setRoles([User::ROLE_USER]);

        $this->entityManager->persist($user);

        $company->addReview($review);

        $this->companyService->addReview($company, $review, $user);

        $this->assertTrue($review->isWait());
        $this->assertFalse($review->isActive());
        $this->assertTrue($review->getUser()->getId() == $user->getId());
        $this->assertTrue($review->getCompany()->getId() == $company->getId());
        $this->assertNull($company->getAssessment());

        // Admin create

        $user->setRoles([User::ROLE_ADMIN]);
        $this->entityManager->persist($user);

        $review = new Review();
        $review->setText('Good Review 2');
        $review->setAssessment(2);

        $company->addReview($review);

        $this->companyService->addReview($company, $review, $user);

        $this->assertTrue($review->isActive());
        $this->assertFalse($review->isWait());

        $this->assertEquals(2, $company->getAssessment());
        $this->assertEquals(2, count($company->getReviews()->toArray()));
    }

    public function testVerification()
    {
        $company = new Company();
        $company->setPhone('+79997778855');
        $company->setName('Test Company 4');

        $this->companyService->create($company);

        $review = new Review();
        $review->setText('Good Review');
        $review->setAssessment(3);

        $user = new User();
        $user->setUsername('User 4');
        $user->setRoles([User::ROLE_USER]);

        $this->entityManager->persist($user);

        $company->addReview($review);

        $this->companyService->addReview($company, $review, $user);

        $this->assertNull($company->getAssessment());
        $this->assertTrue($review->isWait());
        $this->assertFalse($review->isActive());

        $this->reviewService->verify($review);

        $review2 = new Review();
        $review2->setText('Excellent Review');
        $review2->setAssessment(5);

        $company->addReview($review2);

        $this->companyService->addReview($company, $review2, $user);

        $this->assertEquals(3, $company->getAssessment());
        $this->assertTrue($review->isActive());
        $this->assertFalse($review->isWait());

        $this->assertTrue($review2->isWait());
        $this->assertFalse($review2->isActive());

        $this->reviewService->verify($review2);

        $this->assertEquals(4, $company->getAssessment());
        $this->assertTrue($review->isActive());
        $this->assertFalse($review->isWait());

        $this->assertTrue($review2->isActive());
        $this->assertFalse($review2->isWait());

        $this->reviewService->reject($review);
        $this->assertTrue($review->isRejected());
        $this->assertFalse($review->isActive());

        $this->assertTrue($review2->isActive());
        $this->assertFalse($review2->isWait());

        $this->assertEquals(5, $company->getAssessment());

        $this->reviewService->reject($review2);
        $this->assertTrue($review->isRejected());
        $this->assertFalse($review->isActive());

        $this->assertTrue($review2->isRejected());
        $this->assertFalse($review2->isActive());

        $this->assertNull($company->getAssessment());
    }
}
