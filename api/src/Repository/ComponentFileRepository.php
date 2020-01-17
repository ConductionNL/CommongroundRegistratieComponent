<?php

namespace App\Repository;

use App\Entity\ComponentFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ComponentFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComponentFile[]    findAll()
 * @method ComponentFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComponentFile::class);
    }

    // /**
    //  * @return ComponentFile[] Returns an array of ComponentFile objects
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
    public function findOneBySomeField($value): ?ComponentFile
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
