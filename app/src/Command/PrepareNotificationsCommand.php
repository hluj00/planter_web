<?php

namespace App\Command;

use App\Repository\AirTemperatureRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
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

    /**
     * @var PlanterRepository
     */
    private $planterRepository;

    /**
     * @var PlantPresetsRepository
     */
    private $plantPresetsRepository;

    /**
     * @var AirTemperatureRepository
     */
    private $airTemperatureRepository;

    public function __construct(
        UserSettingsRepository $userSettingsRepository,
        PlanterRepository $planterRepository,
        PlantPresetsRepository $plantPresetsRepository,
        AirTemperatureRepository $airTemperatureRepository

    )
    {
        $this->userSettingsRepository = $userSettingsRepository;
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
        $this->airTemperatureRepository = $airTemperatureRepository;

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
        foreach ($settings as $UserSetting){
            echo  $UserSetting;
            $userId = $UserSetting->getUserId();
            $planters = $this->planterRepository->findByUserId($userId);
            foreach ($planters as $planter){
                $plantPresets = $this->plantPresetsRepository->findOneById($planter->getPlantPresetsId());
                echo $this->checkTemperature($planter->getId(), $plantPresets->getTemperature());
            }

        }



        return Command::SUCCESS;
    }

    private function checkTemperature($planterId, $minTemp){
        $from = new \DateTime();
        $from->sub(new DateInterval('PT1H'));
        $from->setTime(0,0,0);
        $to = $from;
        $to->setTime(23,59,59);
        $result = $this->airTemperatureRepository->findByPlanterIdDateAndValue($planterId, $from, $to, $minTemp);

        return is_null($result) ? "jop" : "nope";
    }
}
