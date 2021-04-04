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
            ->getResult();
    }

    /**
     * @return AirTemperature[] Returns an array of AirTemperature objects
     */
    public function findByPlanterIdDateAndValue($id, $from, $to, $value)
    {
            $result = $this->createQueryBuilder('a')
                //->andWhere('a.planter_id = :id')
                ->andWhere('a.value < 3')
                //->andWhere('a.date BETWEEN :from AND :to')
                //->setParameter('from', $from)
                //->setParameter('to', $to)
                //->setParameter('id', $id)
                //->setParameter('val', $value)
                ->getQuery()
                ;

            //echo $result;

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
