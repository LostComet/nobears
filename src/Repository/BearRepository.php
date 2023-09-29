<?php

namespace App\Repository;

use App\Entity\Bear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bear>
 *
 * @method Bear|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bear|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bear[]    findAll()
 * @method Bear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bear::class);
    }

//    /**
//     * @return Bear[] Returns an array of Bear objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bear
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
