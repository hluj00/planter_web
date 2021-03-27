<?php

namespace App\Repository;

use App\Entity\Planter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Planter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planter[]    findAll()
 * @method Planter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planter::class);
    }

    // /**
    //  * @return Planter[] Returns an array of Planter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Planter
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
