<?php

namespace App\Repository;

use App\Entity\SettingsPlant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsPlant|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsPlant|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsPlant[]    findAll()
 * @method SettingsPlant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsPlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsPlant::class);
    }

    // /**
    //  * @return SettingsPlant[] Returns an array of SettingsPlant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SettingsPlant
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
