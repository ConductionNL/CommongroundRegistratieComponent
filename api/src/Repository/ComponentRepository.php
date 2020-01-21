<?php

namespace App\Repository;

use App\Entity\Component;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Component|null find($id, $lockMode = null, $lockVersion = null)
 * @method Component|null findOneBy(array $criteria, array $orderBy = null)
 * @method Component[]    findAll()
 * @method Component[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Component::class);
    }

    // /**
    //  * @return Component[] Returns an array of Component objects
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
    public function findOneBySomeField($value): ?Component
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // When updating components we want to update a max of 10 components that have not yet been updated today
    public function findUpdatable()
    {
        return $this->createQueryBuilder('c')
        ->where('c.updatedAt < :now')
        ->setParameter(':now', new \DateTime())
        ->orderBy('c.updatedAt', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    }

    // When updating components we want to update a max of 10 components that have not yet been updated today
    public function findCheckable()
    {
        return $this->createQueryBuilder('c')
        ->where('c.checked < c.updatedExternal')
        ->orWhere('c.checked is null')
        ->orderBy('c.updatedExternal', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    }
}
