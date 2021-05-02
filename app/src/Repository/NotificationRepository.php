<?php

namespace App\Repository;

use App\Entity\Notification;
use DateTimeZone;
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
     * @param $send
     * @param $date
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
     * @param $planterId
     * @param $date
     * @param $type
     * @return Notification[] Returns an array of Notification objects
     */
    public function findNewest($planterId, $date, $type): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.planter_id = :val')
            ->andWhere('n.type = :type')
            ->andWhere('n.created_at > :date')
            ->setParameter('val', $planterId)
            ->setParameter('type', $type)
            ->setParameter('date', $date)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $planterId
     * @param $type
     * @return Notification[] Returns an array of Notification objects
     * @throws \Exception
     */
    public function findTodayNotifications($planterId, $type): array
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->setTime(0,0,0);

        return $this->createQueryBuilder('n')
            ->andWhere('n.planter_id = :planter')
            ->andWhere('n.type = :type')
            ->andWhere('n.created_at > :date')
            ->setParameter('planter', $planterId)
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
