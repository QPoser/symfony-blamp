<?php

namespace App\Tests;

use App\Entity\Company\BusinessRequest;
use App\Entity\Company\Company;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Services\AuthService;
use App\Services\CompanyService;
use App\Services\UserService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyTest extends KernelTestCase
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CompanyService
     */
    private $companyService;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->companyService = $kernel->getContainer()->get(CompanyService::class);
    }

    public function testCreate()
    {
        $company = new Company();
        $company->setPhone('+79997778855');
        $company->setName('Test Company');

        $this->companyService->create($company);

        $this->assertTrue($company->isWait());
        $this->assertFalse($company->isActive());

        $company->setRejectReason('Причина отклонения');
        $this->companyService->reject($company);
        $this->assertFalse($company->isActive());
        $this->assertFalse($company->isWait());
        $this->assertTrue($company->isRejected());

        $this->companyService->verify($company);

        $this->assertTrue($company->isActive());
        $this->assertFalse($company->isWait());

        $this->assertEquals($company->getRejectReason(), 'Причина отклонения');
    }

    public function testAddingReview()
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
        $this->entityManager->flush();

        $this->companyService->addReview($company, $review, $user);

        $this->assertTrue($review->isWait());
        $this->assertFalse($review->isActive());
        $this->assertTrue($review->getUser()->getId() == $user->getId());
        $this->assertTrue($review->getCompany()->getId() == $company->getId());
    }

    public function testBusinessRequests()
    {
        $company = new Company();
        $company->setPhone('+79997778855');
        $company->setName('Test Company 3');

        $this->companyService->create($company);

        $user = new User();
        $user->setUsername('User1');
        $user->setRoles([User::ROLE_USER]);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $request = new BusinessRequest();
        $request->setPhone('+79998885544');
        $request->setNote('123');
        $request->setUser($user);
        $request->setCompany($company);
        $this->companyService->addRequest($request, $company, $user);

        $this->assertTrue($request->isWait());
        $this->assertFalse($request->isSuccess());
        $this->assertFalse($request->isRejected());

        $this->assertFalse($company->getBusinessUsers()->contains($user));

        $this->companyService->attachUser($request);

        $this->assertTrue($request->isSuccess());
        $this->assertFalse($request->isWait());
        $this->assertFalse($request->isRejected());

        $this->assertTrue($company->getBusinessUsers()->contains($user));

        $this->companyService->deattachUser($company, $user);

        $this->assertFalse($company->getBusinessUsers()->contains($user));
    }
}
