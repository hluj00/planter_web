<?php

namespace App\Command;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\PlanterRepository;
use App\Repository\UserSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Symfony\Component\Translation\t;

class SendNotificationsCommand extends Command
{
    private $client;
    private $userSettingsRepository;
    private $planterRepository;
    private $notificationRepository;
    private $entityManager;

    protected static $defaultName = 'app:send-notifications';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(
        string $name = null,
        HttpClientInterface $client,
        UserSettingsRepository $userSettingsRepository,
        PlanterRepository $planterRepository,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userSettingsRepository = $userSettingsRepository;
        $this->planterRepository = $planterRepository;
        $this->notificationRepository = $notificationRepository;
        $this->entityManager = $entityManager;
        $this->client = $client;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $notifications = $this->notificationRepository->findBySendAndDate(0, new \DateTime('now'));

        foreach ($notifications as $notification){
            $this->sendNotification($notification);
        }
        $this->entityManager->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    protected function sendNotification(Notification $notification){
        $options = [
            "headers" => ["Content-Type: application/json"],
            'body' => $notification->getValue()
        ];
        $url = $this->userSettingsRepository->findOneByUserId($notification->getUserId())->getIftttEndpoint();

        $this->client->request('POST', $url, $options);

        $notification->setSend(1);
    }

}
