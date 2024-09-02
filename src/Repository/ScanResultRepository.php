<?php

namespace App\Repository;

use App\Entity\ScanResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScanResult>
 *
 * @method ScanResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScanResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScanResult[]    findAll()
 * @method ScanResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScanResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScanResult::class);
    }

//    /**
//     * @return ScanResult[] Returns an array of ScanResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ScanResult
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
