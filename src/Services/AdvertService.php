<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 26.07.18
 * Time: 1:33
 */

namespace App\Services;


use App\Entity\Advert\AdvertDescription;
use App\Entity\Advert\Banner;
use App\Entity\Advert\LogBanner;
use App\Entity\Company\Company;
use App\Entity\User;
use App\Repository\Advert\BannerRepository;
use App\Repository\Advert\LogBannerRepository;
use App\Services\App\EmailService;
use DateTime;
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
    /**
     * @var LogBannerRepository
     */
    private $logs;
    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(EntityManager $manager, Container $container, LogBannerRepository $logs, EmailService $emailService)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->logs = $logs;
        $this->emailService = $emailService;
    }

    public function getRandomBanner(BannerRepository $bannerRepository, $format = Banner::FORMAT_VERTICAL, $user)
    {
        $banners = [];
        if ($format == Banner::FORMAT_VERTICAL) {
            $banners = $bannerRepository->getVerticalBanners();
        } elseif ($format == Banner::FORMAT_HORIZONTAL) {
            $banners = $bannerRepository->getHorizontalBanners();
        }

        if (empty($banners)) {
            return false;
        }

        $banner = $banners[array_rand($banners)];

        if (!$this->isSeen($banner, $user)) {
            $banner->addView();
        }

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

        if ($email = $banner->getUser()->getEmail()) {
            $this->emailService->sendSimpleMessage('Ваш баннер был успешно оплачен!', 'Ваш баннер был успешно оплачен и добавлен на сайт Blamp!', $email);
        }
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

        if ($email = $banner->getUser()->getEmail()) {
            $this->emailService->sendSimpleMessage('Ваш баннер был успешно проверифицирован!',
                'Ваш баннер был успешно проверифицирован и готов к оплате!',
                $email);
        }
    }

    public function rejectBanner(Banner $banner)
    {
        $banner->setStatus(Banner::STATUS_REJECTED);

        $this->manager->flush();

        if ($email = $banner->getUser()->getEmail()) {
            $this->emailService->sendSimpleMessage('Ваш баннер был отклонен!',
                'Ваш баннер был отклонен, и недоступен для публикации!',
                $email);
        }
    }



    // Dynamic Descriptions

    public function addDescription(AdvertDescription $description, Company $company)
    {
        $description->setStatus(AdvertDescription::STATUS_WAIT);
        $description->setCompany($company);

        $this->manager->persist($description);
        $this->manager->flush();
    }

    public function verifyDescription(AdvertDescription $description)
    {
        $description->setStatus(AdvertDescription::STATUS_READY_TO_PAY);

        $this->manager->flush();
    }

    public function rejectDescription(AdvertDescription $description)
    {
        $description->setStatus(AdvertDescription::STATUS_REJECTED);

        $this->manager->flush();
    }

    public function payDescription(AdvertDescription $description)
    {
        $description->setStatus(AdvertDescription::STATUS_ACTIVE);
        $description->setEndDate(new DateTime('now + 1 month'));

        $this->manager->flush();
    }





    private function isSeen(Banner $banner, $user)
    {
        if ($user instanceof User) {
            $logRow = $this->logs->findOneBy(['user' => $user->getId(), 'banner' => $banner->getId()]);
        } else {
            $logRow = $this->logs->findOneBy(['ip' => $user, 'banner' => $banner->getId()]);
        }

        if ($logRow) {
            if ($logRow->getSeenAt() < new \DateTime('now - 1 hour')) {
                $logRow->setSeenAt(new DateTime());
                $this->manager->flush();
                return false;
            }
            return true;
        }

        $this->fixLog($banner, $user);

        return false;
    }

    private function fixLog(Banner $banner, $user)
    {
        $logRow = new LogBanner();
        $logRow->setSeenAt(new DateTime());
        $logRow->setBanner($banner);
        if ($user instanceof User) {
            $logRow->setUser($user);
        } else {
            $logRow->setIp($user);
        }
        $this->manager->persist($logRow);
        $this->manager->flush();
    }

}