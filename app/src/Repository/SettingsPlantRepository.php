<?php

namespace App\Repository;

use App\Entity\SettingsPlant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
    //  * @return SettingsPlant[] Returns an array of SettingsPlant objects
    //  */
    public function findByUserId($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user_id = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id_settings_plant', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneByIdAndUserId($id): ?SettingsPlant
    {
        try {
            return $this->createQueryBuilder('s')
                ->andWhere('s.id_settings_plant = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
            // throw err neni unique
        }
    }

}
