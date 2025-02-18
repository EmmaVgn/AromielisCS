<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
    * @return Order[] Returns an array of Order objects
    */
    public function findPaidOrdersByUser($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state > :val')
            ->andWhere('o.user = :user')
            ->setParameter('val', 0)
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Define custom methods if needed
    public function findOneByReference($reference)
    {
        return $this->findOneBy(['reference' => $reference]);
    }

    public function findOneByStripeSession($stripeSession)
    {
        return $this->findOneBy(['stripeSession' => $stripeSession]);
    }       


    //stats des orders
    public function countOrdersByMonth(): array
    {
        return $this->createQueryBuilder('o')
            ->select("SUBSTRING(o.createdAt, 1, 7) AS month, COUNT(o.id) AS orderCount")
            ->where('o.state > 0') // Seules les commandes validées
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    public function countRevenueByMonth(): array
    {
        return $this->createQueryBuilder('o')
            ->select("SUBSTRING(o.createdAt, 1, 7) AS month, SUM(o.total) AS totalRevenue")
            ->where('o.state > 0') // Seules les commandes validées
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    public function getRevenueBySource(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.referrer, COUNT(o.id) as orderCount, SUM(o.totalAmount) as totalRevenue')
            ->where('o.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('o.referrer')
            ->getQuery()
            ->getResult();
    }
    



}
    
