<?php

namespace App\Repository\Admin;

use App\Entity\Admin\setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method setting[]    findAll()
 * @method setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class settingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, setting::class);
    }

    // /**
    //  * @return setting[] Returns an array of setting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?setting
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
