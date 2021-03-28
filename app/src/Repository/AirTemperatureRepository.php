<?php

namespace App\Repository;

use App\Entity\AirTemperature;
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
    public function findByPlanterId($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.planter_id = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
