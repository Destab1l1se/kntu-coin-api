<?php

namespace App\Repository;

use App\Entity\BlockTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockTransaction[]    findAll()
 * @method BlockTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockTransaction::class);
    }

    // /**
    //  * @return BlockTransaction[] Returns an array of BlockTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlockTransaction
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
