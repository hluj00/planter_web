<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

     /**
      * @return Notification[] Returns an array of Notification objects
      */
    public function findBySendAndDate($send, $date): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.send = :val')
            ->andWhere('n.send_at < :date')
            ->setParameter('val', $send)
            ->setParameter('date', $date)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

     /**
      * @return Notification[] Returns an array of Notification objects
      */
    public function findByUserIdDateAndType($userId, $date, $type): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user_id = :val')
            ->andWhere('n.type = :type')
            ->andWhere('n.created_at > :date')
            ->setParameter('val', $userId)
            ->setParameter('type', $type)
            ->setParameter('date', $date)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
