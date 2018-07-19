<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AppSetRoleCommand extends Command
{
    protected static $defaultName = 'app:set-role';
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct($name = null, UserRepository $users, EntityManagerInterface $manager)
    {
        parent::__construct($name);
        $this->users = $users;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Set role for user by username')
            ->addArgument('username', InputArgument::REQUIRED, 'User username')
            ->addArgument('role', InputArgument::REQUIRED, 'User role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $user = $this->users->findUserByUsername($username);

        if (!$user) {
            $io->error('User with username ' . $username . ' not found');
            return;
        }

        $role = $input->getArgument('role');

        switch ($role) {
            case 'user':
                $user->becomeUser();
                break;
            case 'business':
                $user->becomeUser();
                break;
            case 'admin':
                $user->becomeUser();
                break;
            default:
                $io->error('Role ' . $role . ' not setted.');
                return;
                break;
        }

        $this->manager->flush();

        $io->success('Success!');
    }
}
