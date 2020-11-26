<?php

namespace App\Repository;

use App\Entity\CommittedTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommittedTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommittedTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommittedTransaction[]    findAll()
 * @method CommittedTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommittedTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommittedTransaction::class);
    }

    // /**
    //  * @return CommittedTransaction[] Returns an array of CommittedTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommittedTransaction
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
