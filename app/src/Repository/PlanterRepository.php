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

     /**
      * @return Planter[] Returns an array of Planter objects
      */
    public function findByUserId($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user_id = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Planter[] Returns an array of Planter objects
     */
    public function findByPlantPresetId($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.plant_presets_id = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Planter[] Returns an array of Planter objects
     */
    public function findPlantersWithSameName($userId, $name, $id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :name')
            ->andWhere('p.user_id = :userId')
            ->andWhere('p.id != :id')
            ->setParameter('userId', $userId)
            ->setParameter('name', $name)
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Planter[] Returns an array of Planter objects
     */
    public function findPlantersByUserIdAndName($userId, $name)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :name')
            ->andWhere('p.user_id = :userId')
            ->setParameter('userId', $userId)
            ->setParameter('name', $name)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param $value
     * @return Planter|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($value): ?Planter
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
