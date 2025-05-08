<?php

namespace App\Repository;

use App\Entity\StockModification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockModification>
 */
class StockModificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockModification::class);
    }

    public function findByStockModificationFiltered($warehouse, $filter): array {
		return $this->createQueryBuilder('sm')
								->where('sm.stockModificationMessage LIKE :filter
                                        AND t.warehouse = :warehouse
											')
								->setParameters(new ArrayCollection([
                                    new Parameter('warehouse', $warehouse),
                                    new Parameter('filter', '%'.$filter.'%')
                                ]))
								->getQuery()
								->getResult();
	}
//    /**
//     * @return StockModification[] Returns an array of StockModification objects
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

//    public function findOneBySomeField($value): ?StockModification
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
