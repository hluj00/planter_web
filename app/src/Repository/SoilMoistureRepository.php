<?php

namespace App\Repository;

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
