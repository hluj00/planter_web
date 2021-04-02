<?php

namespace App\Repository;

use App\Entity\AirHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AirHumidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AirHumidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AirHumidity[]    findAll()
 * @method AirHumidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AirHumidityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AirHumidity::class);
    }

     /**
      * @return AirHumidity[] Returns an array of AirHumidity objects
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
    public function findOneBySomeField($value): ?AirHumidity
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
