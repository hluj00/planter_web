<?php

namespace App\Repository;

use App\Entity\PlantPresets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlantPresets|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlantPresets|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlantPresets[]    findAll()
 * @method PlantPresets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantPresetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlantPresets::class);
    }

    /**
    //  * @return SettingsPlant[] Returns an array of SettingsPlant objects
    //  */
    public function findByUserId($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user_id = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneById($id): ?PlantPresets
    {
        try {
            return $this->createQueryBuilder('s')
                ->andWhere('s.id = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
            // throw err neni unique
        }
    }

}
