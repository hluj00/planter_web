<?php

namespace App\Command;

use App\Entity\Action;
use App\Entity\PlantPresets;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\SoilMoistureRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateActionsCommand extends Command
{
    /**
     * @var SoilMoistureRepository
     */
    private $soilMoistureRepository;

    private $planterRepository;
    private $plantPresetsRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected static $defaultName = 'app:create-actions';
    protected static $defaultDescription = 'Add a short description for your command';

    protected static $ACTION_RUN_PUMP = 1;


    public function __construct(
        string $name = null,
        PlanterRepository $planterRepository,
        PlantPresetsRepository $plantPresetsRepository,
        SoilMoistureRepository $soilMoistureRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
        $this->soilMoistureRepository = $soilMoistureRepository;
        $this->entityManager = $entityManager;

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
        $planters = $this->planterRepository->findAll();
        $plantPresets = [];

        foreach ($planters as $planter){
            $presetId = $planter->getPlantPresetsId();
            if (isset($plantPresets[$presetId])){
                $plantPreset = $plantPresets[$presetId];
            }else{
                $plantPreset = $plantPresets[$presetId] = $this->plantPresetsRepository->findOneById($presetId);
            }

            if ($this->lowMoisture($planter->getId(), $plantPreset)){
                echo "vytvari \n";
                $this->createAction($planter->getId());
            }else{
                echo "NEvytvari \n";
            }
        }

        return 0;
    }


    protected function lowMoisture($planterId, PlantPresets $plantPreset): bool{
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->setTime(0,0,0);

        $soilMoisture = $this->soilMoistureRepository->findLastByPlanterIdAndDate($planterId, $date);
        $limit = $plantPreset->getMoisture();
        echo $limit;
        if (is_null($soilMoisture)){
            echo "NULL\n";
        }else{
            echo $soilMoisture->getValue();
        }
        return (!(is_null($soilMoisture) || $soilMoisture->getValue() > $limit));
    }

    protected function createAction($planterId){
        $now = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $action = new Action();
        $action->setType(self::$ACTION_RUN_PUMP);
        $action->setCreatedAt($now);
        $action->setPlanterId($planterId);
        $action->setExecuted(false);
        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }
}
