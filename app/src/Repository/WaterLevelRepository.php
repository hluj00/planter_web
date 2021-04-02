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
      * @return WaterLevel[] Returns an array of WaterLevel objects
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
    public function findOneBySomeField($value): ?WaterLevel
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
