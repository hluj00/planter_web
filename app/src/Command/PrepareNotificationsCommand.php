<?php

namespace App\Command;

use App\Repository\UserSettingsRepository;
use DateInterval;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PrepareNotificationsCommand extends Command
{
    protected static $defaultName = 'app:prepare-notifications';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     * @var UserSettingsRepository
     */
    private $userSettingsRepository;

    public function __construct(UserSettingsRepository $userSettingsRepository)
    {
        $this->userSettingsRepository = $userSettingsRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('analyses data and prepares notifications to be send.')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = new \DateTime('1970-01-01');
        $to = new \DateTime('1970-01-01');
        $to->add(new DateInterval('PT1H'));


        $settings = $this->userSettingsRepository->findByNotificationsBetween($from, $to);

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        echo "no ty kokos";
        foreach ($settings as $setting){
            echo  $setting;
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
