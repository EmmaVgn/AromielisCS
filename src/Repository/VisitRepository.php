<?php

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function countBySource(): array
    {
        return $this->createQueryBuilder('v')
            ->select('COALESCE(v.referrer, \'Direct\') AS source, COUNT(v.id) as visitCount')
            ->groupBy('source')
            ->orderBy('visitCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    public function countBySourceWithFilter(string $filter): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COALESCE(v.referrer, \'Direct\') AS source, COUNT(v.id) as visitCount');
    
        if ($filter === 'week') {
            $qb->where('v.visitedAt >= :date')
               ->setParameter('date', (new \DateTime())->modify('-7 days'));
        } elseif ($filter === 'month') {
            $qb->where('v.visitedAt >= :date')
               ->setParameter('date', (new \DateTime('first day of this month')));
        } elseif ($filter === 'year') {
            $qb->where('v.visitedAt >= :date')
               ->setParameter('date', (new \DateTime('first day of January this year')));
        }
    
        return $qb->groupBy('source')
                  ->orderBy('visitCount', 'DESC')
                  ->getQuery()
                  ->getResult();
    
    }

    
    


    //    /**
    //     * @return Visit[] Returns an array of Visit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Visit
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
