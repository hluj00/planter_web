<?php

namespace App\Command;

use App\Entity\PlantPresets;
use App\Entity\UserSettings;
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

class PrepareDailyNotificationsCommand extends Command
{
    protected static $defaultName = 'app:prepare-daily-notifications';
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $settings = $this->userSettingsRepository->findAll();

        foreach ($settings as $userSetting){
            $userId = $userSetting->getUserId();
            $planters = $this->planterRepository->findByUserId($userId);

            $sendAt = $userSetting->getSendNotificationsAtToday();
            $sendAtHour = $userSetting->getSendNotificationsAtToday()->format('H');
            $now = new \DateTime('now', new DateTimeZone('Europe/Prague'));
            $nowHour = $now->format('H');


            if ( $sendAtHour - $nowHour == 1) {
                foreach ($planters as $planter) {
                    $planterId = $planter->getId();
                    $plantPresets = $this->plantPresetsRepository->findOneById($planter->getPlantPresetsId());
                    $period = $userSetting->getNotificationPeriodType();

                    $x = $this->checkLight($planter->getId(), $plantPresets, $period);
                    if ($x) {
                        $body = sprintf('{ "value1" : "%s", "value2" : "didn\'t get enough light yesterday" }', $planter->getName());
                        $this->createNewNotification($userId, $planterId, $sendAt, $body, Notification::$TYPE_LIGHT_LEVEL);
                    }
                }
            }

        }

        return Command::SUCCESS;
    }

    private function createNewNotification($userId,$planterId,$sendAt,$body,$type){
        $notification = new Notification();
        $notification->setUserId($userId);
        $notification->setPlanterId($planterId);
        $notification->setCreatedAt(new \DateTime('now', new DateTimeZone('Europe/Prague')));
        $notification->setSendAt($sendAt);
        $notification->setType($type);
        $notification->setValue($body);
        $notification->setSend(false);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $message = sprintf("creating low light notification for planter ID: %s", $planterId);
        $this->log($message);
    }


    private function checkLight(int $planterId, PlantPresets $plantPresets, $periodType): bool{
        $minVaue = $plantPresets->getLightLevel();
        $minTime = $plantPresets->getLightDuration() * 3600;

        //get data from db
        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));

        if ($periodType == UserSettings::$PERIOD_PREVIOUS_DAY) {
            $from->modify('-1 day');
            $to->modify('-1 day');
            $from->setTime(0, 0, 0);
            $to->setTime(23, 59, 59);
        }else{
            $from->modify('-1 day');
        }

        $data = $this->lightLevelRepository->findByPlanterIdAndDates($planterId, $from, $to);

        $count = count($data);
        if ($count < 2){
            return false;
        }

        $timeTotal = 0;
        for ($i = 1; $i< $count; $i++){
            if ($data[$i-1]->getValue() + $data[$i-1]->getValue() > $minVaue * 2){
                $time1 = strtotime($data[$i-1]->getDate()->format('Y-m-d H:i:s'));
                $time2 = strtotime($data[$i]->getDate()->format('Y-m-d H:i:s'));
                $timeTotal += $time2 - $time1;
            }
        }

        return($timeTotal < $minTime);
    }

    private function log($message){
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date = $date->format('Y-m-d H:i:s');
        echo sprintf("[%s] %s\n",$date, $message);
    }
}
