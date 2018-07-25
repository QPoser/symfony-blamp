<?php

namespace App\Command;

use App\Entity\Company\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFixAssessmentCommand extends Command
{
    protected static $defaultName = 'app:fix-assessments';
    /**
     * @var CompanyRepository
     */
    private $companies;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct($name = null, CompanyRepository $companies, EntityManagerInterface $manager)
    {
        parent::__construct($name);
        $this->companies = $companies;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $companies = $this->companies->findAll();

        /** @var Company $company */
        foreach ($companies as $company) {
            $company->setFixedAssessment($company->getAssessment());
        }

        $this->manager->flush();

        $io->success('Success!');
    }
}
