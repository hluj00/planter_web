<?php

namespace App\Repository;

use App\Entity\LightLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LightLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method LightLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method LightLevel[]    findAll()
 * @method LightLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LightLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LightLevel::class);
    }

     /**
      * @return LightLevel[] Returns an array of LightLevel objects
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
     * @return LightLevel[] Returns an array of AirTemperature objects
     */
    public function findByPlanterIdAndDates($id, $from, $to)
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :id')
            ->andWhere('a.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $result->getResult();
    }

    /*
    public function findOneBySomeField($value): ?LightLevel
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
