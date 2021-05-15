<?php

namespace App\Repository;

use App\Entity\WaterLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WaterLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaterLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method WaterLevel[]    findAll()
 * @method WaterLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaterLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WaterLevel::class);
    }

    /**
     * @return WaterLevel[] Returns an array of AirTemperature objects
     */
    public function findByPlanterIdDatesAndValue($id, $from, $to, $value)
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :id')
            ->andWhere('a.value < :val')
            ->andWhere('a.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('id', $id)
            ->setParameter('val', $value)
            ->getQuery()
        ;

        return $result->getResult();
    }

     /**
      * @return WaterLevel[] Returns an array of WaterLevel objects
      */
    public function findByPlanterIdAndDate($id, $from)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->andWhere('a.date > :date')
            ->setParameter('val', $id)
            ->setParameter('date', $from)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return WaterLevel[] Returns an array of WaterLevel objects
     */
    public function findLastInTenMinutes($planterId)
    {
        $date = new \DateTime();
        $date->modify('-10 minutes');

        return $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->andWhere('a.date > = :date')
            ->setParameter('val', $planterId)
            ->setParameter('date', $date)
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
