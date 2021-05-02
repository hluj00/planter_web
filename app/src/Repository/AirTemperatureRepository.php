<?php

namespace App\Repository;

use App\Entity\AirTemperature;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AirTemperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method AirTemperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method AirTemperature[]    findAll()
 * @method AirTemperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AirTemperatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AirTemperature::class);
    }

    /**
     * @return AirTemperature[] Returns an array of AirTemperature objects
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
     * @return AirTemperature[] Returns an array of AirTemperature objects
     */
    public function findLastInTenMinutes($value)
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->modify('-10 minutes');

        return $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->andWhere('a.date > = :date')
            ->setParameter('val', $value)
            ->setParameter('date', $date)
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AirTemperature[] Returns an array of AirTemperature objects
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
     * @return AirTemperature[] Returns an array of AirTemperature objects
     */
    public function findByPlanterIdDate($id, $from)
    {
            $result = $this->createQueryBuilder('a')
                ->andWhere('a.planter_id = :id')
                ->andWhere('a.date > :from ')
                ->setParameter('from', $from)
                ->setParameter('id', $id)
                ->getQuery()
                ;

            return $result->getResult();
    }

    /*
    public function findOneBySomeField($value): ?AirTemperature
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
