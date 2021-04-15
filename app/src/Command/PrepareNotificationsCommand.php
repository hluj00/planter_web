<?php

namespace App\Command;

use App\Entity\PlantPresets;
use App\Repository\AirTemperatureRepository;
use App\Entity\Notification;
use App\Repository\LightLevelRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\UserSettingsRepository;
use DateInterval;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @var LightLevelRepository
     */
    private $lightLevelRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserSettingsRepository $userSettingsRepository,
        PlanterRepository $planterRepository,
        PlantPresetsRepository $plantPresetsRepository,
        AirTemperatureRepository $airTemperatureRepository,
        LightLevelRepository $lightLevelRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userSettingsRepository = $userSettingsRepository;
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
        $this->airTemperatureRepository = $airTemperatureRepository;
        $this->lightLevelRepository = $lightLevelRepository;
        $this->entityManager = $entityManager;

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


        $settings = $this->userSettingsRepository->findAll();

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        echo "no ty kokossss";
        foreach ($settings as $userSetting){
            echo  $userSetting;
            $userId = $userSetting->getUserId();
            $planters = $this->planterRepository->findByUserId($userId);

            $sendAt = $userSetting->getSendNotificationsAtToday();


            foreach ($planters as $planter){
                echo $planter->getName();
                $plantPresets = $this->plantPresetsRepository->findOneById($planter->getPlantPresetsId());

                $x = $this->checkTemperature($planter->getId(), $plantPresets);
                echo $x ? "jop" : "nope";
                if ($x){
                    $body = sprintf('{ "value1" : "%s", "value2" : "is below set temperature" }', $planter->getName());
                    $this->createNewNotification($userId, $sendAt, $body, 1);
                }

                $x = $this->checkLight($planter->getId(), $plantPresets);
                echo $x ? "jop" : "nope";
                if ($x){
                    $body = sprintf('{ "value1" : "%s", "value2" : "didn\'t get enough light yesterday" }', $planter->getName());
                    $this->createNewNotification($userId, $sendAt, $body, 2);
                }
            }

        }

        return Command::SUCCESS;
    }

    private function createNewNotification($userId,$sendAt,$body,$type){
        $notification = new Notification();
        $notification->setUserId($userId);
        $notification->setCreatedAt(new \DateTime('now', new DateTimeZone('Europe/Prague')));
        $notification->setSendAt($sendAt);
        $notification->setType(2);
        $notification->setValue($body);
        $notification->setSend(false);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    private function checkTemperature(int $planterId, PlantPresets $plantPresets): bool
    {
        $minTemp = $plantPresets->getTemperature();

        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $from->sub(new DateInterval('P1D'));
        $from->setTime(0,0,0);
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $to->sub(new DateInterval('P1D'));
        $to->setTime(23,59,59);
        $result = $this->airTemperatureRepository->findByPlanterIdDatesAndValue($planterId, $from, $to, $minTemp);

        return !empty($result);
    }

    private function checkLight(int $planterId, PlantPresets $plantPresets): bool{
        $minVaue = $plantPresets->getLightLevel();
        $minTime = $plantPresets->getLightDuration() * 3600;

        //get data from db
        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $from->sub(new DateInterval('P1D'));
        $from->setTime(0,0,0);
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $to->sub(new DateInterval('P1D'));
        $to->setTime(23,59,59);
        $data = $this->lightLevelRepository->findByPlanterIdAndDates($planterId, $from, $to);


        $size = sizeof($data);
        $timeTotal = 0;
        for ($i = 1; $i< $size; $i++){
            if ($data[$i-1]->getValue() + $data[$i-1]->getValue() > $minVaue * 2){
                $time1 = strtotime($data[$i-1]->getDate()->format('Y-m-d H:i:s'));
                $time2 = strtotime($data[$i]->getDate()->format('Y-m-d H:i:s'));
                $timeTotal += $time2 - $time1;
            }
        }

        return($timeTotal < $minTime);
    }
}
