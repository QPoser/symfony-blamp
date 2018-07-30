<?php

namespace App\Command;

use App\Entity\Advert\AdvertDescription;
use App\Entity\Company\Company;
use App\Repository\Advert\AdvertDescriptionRepository;
use App\Repository\Company\CompanyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppCloseAdvertDescriptionsCommand extends Command
{
    protected static $defaultName = 'app:close-adverts';
    /**
     * @var AdvertDescriptionRepository
     */
    private $descriptions;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct($name = null, AdvertDescriptionRepository $descriptions, EntityManagerInterface $manager)
    {
        parent::__construct($name);
        $this->descriptions = $descriptions;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Данная команда закрывает динамические описания, которые истекли.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $descriptions = $this->descriptions->getActiveDescriptions();

        /** @var Company $company */
        foreach ($descriptions as $description) {
            if ($description->getEndDate() < new DateTime()) {
                $description->setStatus(AdvertDescription::STATUS_READY_TO_PAY);
            }
        }

        $this->manager->flush();

        $io->success('Success!');
    }
}
