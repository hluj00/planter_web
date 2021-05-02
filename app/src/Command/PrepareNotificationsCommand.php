<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\PlantPresets;
use App\Repository\AirTemperatureRepository;
use App\Repository\NotificationRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\UserSettingsRepository;
use App\Repository\WaterLevelRepository;
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
     * @var WaterLevelRepository
     */
    private $waterLevelRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var AirTemperatureRepository
     */
    private $airTemperatureRepository;

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function __construct(
        UserSettingsRepository $userSettingsRepository,
        PlanterRepository $planterRepository,
        PlantPresetsRepository $plantPresetsRepository,
        WaterLevelRepository $waterLevelRepository,
        AirTemperatureRepository $airTemperatureRepository,
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository
    )
    {
        $this->userSettingsRepository = $userSettingsRepository;
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
        $this->waterLevelRepository = $waterLevelRepository;
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
        $this->airTemperatureRepository = $airTemperatureRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $settings = $this->userSettingsRepository->findAll();

        $timeNow = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        foreach ($settings as $userSetting){
            $userId = $userSetting->getUserId();
            $planters = $this->planterRepository->findByUserId($userId);
            foreach ($planters as $planter){
                $plantPresets = $this->plantPresetsRepository->findOneById($planter->getPlantPresetsId());
                $planterId = $planter->getId();

                $lowWater = $this->lowWater($planterId);
                $waterNotificationAlreadyCreated = $this->notificationCreatedToday($planterId, Notification::$TYPE_WATER_LEVEL);

                if ($lowWater && !$waterNotificationAlreadyCreated){
                    echo "sending\n";
                    $sendAt = $this->notificationSendYesterday($planterId,3) ? $userSetting->getSendNotificationsAtToday() : $timeNow;
                    $body = sprintf('{ "value1" : "%s", "value2" : "is running low on water" }', $planter->getName());
                    $this->createNewNotification($userId, $planterId, $sendAt, $body, Notification::$TYPE_WATER_LEVEL);
                }

                $lowTemp = $this->lowTemperature($planterId, $plantPresets);
                $shouldSend = $this->shouldSendTemperatureNotification($planterId);

                if ($lowTemp && $shouldSend){
                    echo "sending\n";
                    $body = sprintf('{ "value1" : "%s", "value2" : "is below minimal temperature" }', $planter->getName());
                    $this->createNewNotification($userId, $planterId, $timeNow, $body, Notification::$TYPE_TEMPERATURE);
                }
            }
        }
        return Command::SUCCESS;
    }

    protected function lowWater($planterId){
        $water = $this->waterLevelRepository->findLastInTenMinutes($planterId);
        return ( isset($water[0]) && $water[0]->getValue() < 50 );
    }

    protected function lowTemperature($planterId,PlantPresets $plantPresets){
        $water = $this->airTemperatureRepository->findLastInTenMinutes($planterId);
        $minTemp = $plantPresets->getTemperature();
        return ( isset($water[0]) && $water[0]->getValue() < $minTemp );
    }

    private function notificationCreatedToday($planterId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));;
        $date->setTime(0,0,0);
        $result = $this->notificationRepository->findNewest($planterId ,$date ,$notifType);
        return !empty($result);
    }

    private function shouldSendTemperatureNotification($planterId){
        $notificationAlreadyCreated = $this->notificationCreatedInLatTenMinutes($planterId, Notification::$TYPE_TEMPERATURE);
        if ($notificationAlreadyCreated)
            return false;

        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));;
        $from->modify('-1 day');
        $result = $this->notificationRepository->findNewest($planterId ,$from ,Notification::$TYPE_TEMPERATURE);
        if ( isset($result[0]) ){
            $lastNotif = $result[0]->getCreatedAt();
            $temperatures = $this->airTemperatureRepository->findByPlanterIdDate($planterId, $lastNotif);
            $min = $max = $temperatures[0]->getValue();
            foreach ($temperatures as $temperature){
                if ($temperature->getValue() > $max){
                    $max = $temperature->getValue();
                }else if($temperature->getValue() < $min){
                    $min = $temperature->getValue();
                }
            }
            if ( $max - $min < 2){
                return false;
            }
        }
        return true;
    }

    private function notificationCreatedInLatTenMinutes($planterId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));;
        $date->modify('-10 minutes');
        $result = $this->notificationRepository->findNewest($planterId ,$date ,$notifType);
        return !empty($result);
    }

    private function notificationSendYesterday($planterId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->sub(new DateInterval('P1D'));
        $date->setTime(0,0,0);
        $result = $this->notificationRepository->findNewest($planterId ,$date ,$notifType);
        return !empty($result);
    }

    private function createNewNotification($userId, $planterId,$sendAt,$body,$type){
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
    }
}
