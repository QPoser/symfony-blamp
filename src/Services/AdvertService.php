<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 26.07.18
 * Time: 1:33
 */

namespace App\Services;


use App\Entity\Advert\Banner;
use App\Entity\User;
use App\Repository\Advert\BannerRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdvertService
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Container
     */
    private $container;

    public function __construct(EntityManager $manager, Container $container)
    {
        $this->manager = $manager;
        $this->container = $container;
    }

    public function getRandomBanner(BannerRepository $bannerRepository)
    {
        $banners = $bannerRepository->getActiveBanners();

        $banner = $banners[array_rand($banners)];

        $banner->addView();

        if ($banner->getViews() == 0) {
            $banner->setStatus(Banner::STATUS_READY_TO_PAY);
        }

        $this->manager->flush();

        return $banner;
    }

    public function requestBanner(Banner $banner, User $user, UploadedFile $file)
    {
        $banner->setStatus(Banner::STATUS_WAIT);

        $package = new Package(new EmptyVersionStrategy());
        if ($file) {
            $someFileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->container->getParameter('img_banner_dir'), $someFileName);
            $banner->setBannerImg($package->getUrl('uploads/img/banners/' . $someFileName));
        }

        $banner->setUser($user);
        $banner->setViews(0);

        $this->manager->persist($banner);
        $this->manager->flush();
    }

    public function pay(Banner $banner)
    {
        $banner->setStatus(Banner::STATUS_ACTIVE);
        $banner->setViews(1000);

        $this->manager->flush();
    }

    public function addView(Banner $banner)
    {
        $banner->addView();

        $this->manager->flush();
    }

    public function verifyBanner(Banner $banner)
    {
        $banner->setStatus(Banner::STATUS_READY_TO_PAY);

        $this->manager->flush();
    }

    public function rejectBanner(Banner $banner)
    {
        $banner->setStatus(Banner::STATUS_REJECTED);

        $this->manager->flush();
    }

}