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

class PrepareHourlyNotificationsCommand extends Command
{
    protected static $defaultName = 'app:prepare-hourly-notifications';
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

                $notificationExists = $this->notificationAlreadyExists($userId,3);
                $x = $this->checkWaterLevel($planter->getId(), $plantPresets) && !$notificationExists;
                if ($x){
                    $sendAt = $this->notificationSendYesterday($userId,3) ? $userSetting->getSendNotificationsAtToday() : $timeNow;
                    $body = sprintf('{ "value1" : "%s", "value2" : "is running low on water" }', $planter->getName());
                    $this->createNewNotification($userId, $sendAt, $body, Notification::$TYPE_WATER_LEVEL);
                }
            }
        }
        return Command::SUCCESS;
    }

    private function notificationAlreadyExists($userId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));;
        $date->setTime(0,0,0);
        $result = $this->notificationRepository->findByUserIdDateAndType($userId ,$date ,$notifType);
        return !empty($result);
    }

    private function notificationSendYesterday($userId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->sub(new DateInterval('P1D'));
        $date->setTime(0,0,0);
        $result = $this->notificationRepository->findByUserIdDateAndType($userId ,$date ,$notifType);
        return !empty($result);
    }

    private function checkWaterLevel(int $planterId, PlantPresets $plantPresets): bool
    {
        $minValue = 0.15;

        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $from->sub(new DateInterval('PT1H'));
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $result = $this->waterLevelRepository->findByPlanterIdDatesAndValue($planterId, $from, $to, $minValue);

        return !empty($result);
    }

    private function createNewNotification($userId,$sendAt,$body,$type){
        $notification = new Notification();
        $notification->setUserId($userId);
        $notification->setCreatedAt(new \DateTime('now', new DateTimeZone('Europe/Prague')));
        $notification->setSendAt($sendAt);
        $notification->setType($type);
        $notification->setValue($body);
        $notification->setSend(false);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
