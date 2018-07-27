<?php

namespace App\DataFixtures;

use App\Entity\Company\Company;
use App\Entity\Review\Review;
use App\Entity\User;
use App\Services\CommentService;
use App\Services\CompanyService;
use App\Services\ReviewService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var CompanyService
     */
    private $service;

    private $loremSamples = [

        'Nam nec nunc id turpis elementum vehicula. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam et massa eu erat tempor vulputate. Pellentesque laoreet mi vitae justo porttitor accumsan. Nunc ac ligula urna. Sed sem metus turpis duis.',
        'Donec porta pharetra justo, id ultrices lectus venenatis ut. Nunc ac laoreet nulla, nec molestie odio. Maecenas eu eros et purus dignissim volutpat nec non odio. Fusce arcu orci, congue vitae sapien in, ultrices iaculis odio. Etiam rutrum metus.',
        'Maecenas ornare nulla enim, at suscipit nulla viverra vitae. Praesent consequat tellus in justo maximus, nec blandit lorem congue. Praesent porta pulvinar vulputate. Phasellus eu sapien ante. Pellentesque magna neque, vestibulum quis metus amet.',
        'Duis sem magna, aliquam eu cursus sed, viverra quis nulla. Vestibulum malesuada ornare nisl, a euismod libero aliquet a. Fusce pharetra at tellus nec finibus. Aenean a faucibus diam. Aenean porttitor, tellus sit amet venenatis accumsan volutpat.',
        'Ut accumsan imperdiet pharetra. Donec non lectus augue. Nulla facilisi. Maecenas a ex lacinia, porttitor diam sit amet, ornare lacus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Proin rhoncus risus justo, quis pellentesque sed.',

    ];

    public function __construct(CompanyService $companyService )
    {
        $this->service = $companyService;
    }

    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();
        $companyRepo = $manager->getRepository(Company::class);
        $companies = $companyRepo->findAll();
        $statusArray = [Review::STATUS_ACTIVE, Review::STATUS_REJECTED, Review::STATUS_WAIT];

        for ($i = 0; $i < count($companies); $i++) {
            for ($j = 0; $j <= mt_rand(5, 10); $j++) {
                $review = new Review();
                $review->setText($this->loremSamples[mt_rand(0,4)]);
                $review->setAssessment(mt_rand(1, 5));
                $status = $statusArray[array_rand($statusArray)];
                $review->setStatus($status);
                $company = $companies[array_rand($companies)];
                $user = $users[array_rand($users)];

                $this->service->addReviewFixtureMod($company, $review, $user);
            }
        }
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            CompanyFixtures::class,
        );
    }
}
