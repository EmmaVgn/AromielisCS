<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Advise;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Advise>
 */
class AdviseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advise::class);
    }

    public function Advise()
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult();
    }

    public function search(string $searchQuery): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :query OR a.subtitle LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchQueryBuilder(string $searchQuery): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :searchQuery OR a.subtitle LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $searchQuery . '%')
            ->orderBy('a.createdAt', 'DESC');
    }
    
    
    

    public function findByTag(Tag $tag)
    {
        return $this->createQueryBuilder('a')
            ->join('a.tags', 't')
            ->where('t = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }

    // Méthode pour récupérer les advises les plus populaires
    public function findMostPopularAdvises(int $limit)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.views', 'DESC') // Trie par le nombre de vues
            ->setMaxResults($limit) // Limite le nombre de résultats
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Advise[] Returns an array of Advise objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advise
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
