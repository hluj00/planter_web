<?php

namespace App\Repository;

use App\Entity\PlantPresets;
use App\Entity\SoilMoisture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SoilMoisture|null find($id, $lockMode = null, $lockVersion = null)
 * @method SoilMoisture|null findOneBy(array $criteria, array $orderBy = null)
 * @method SoilMoisture[]    findAll()
 * @method SoilMoisture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoilMoistureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoilMoisture::class);
    }

     /**
      * @return SoilMoisture[] Returns an array of SoilMoisture objects
      */
    public function findByPlanterIdAndDate($id, $from, $to)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->andWhere('a.date > :date')
            ->andWhere('a.date < :to')
            ->setParameter('val', $id)
            ->setParameter('date', $from)
            ->setParameter('to', $to)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $planterId
     * @param $date
     * @return SoilMoisture|null
     */
    public function findLastByPlanterIdAndDate($planterId, $date)
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->andWhere('a.date > :date')
            ->setParameter('val', $planterId)
            ->setParameter('date', $date)
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;

        return empty($result) ? null : $result[0];
    }

    /*
    public function findOneBySomeField($value): ?SoilMoisture
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
