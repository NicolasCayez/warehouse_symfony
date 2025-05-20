<?php

namespace App\Repository;

use App\Entity\StockTransfert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockTransfert>
 */
class StockTransfertRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
			parent::__construct($registry, StockTransfert::class);
	}

	public function findAllFiltered($filter): array {
	return $this->createQueryBuilder('st')
							->where('st.stockTransfertMessage LIKE :filter')
							->setParameters(new ArrayCollection([
																	new Parameter('filter', '%'.$filter.'%')
															]))
							->getQuery()
							->getResult();
	}

	// public function findByWarehouseFiltered($warehouse, $filter): array {
	// 	return $this->createQueryBuilder('t')
	// 							->where('t.stockTransfertMessage LIKE :filter
  //                       AND t.warehouse = :warehouse
	// 										')
	// 							->setParameters(new ArrayCollection([
  //                                   new Parameter('warehouse', $warehouse),
  //                                   new Parameter('filter', '%'.$filter.'%')
  //                               ]))
	// 							->getQuery()
	// 							->getResult();
	// }

	public function findByWarehouseFiltered($warehouse, $filter): array {
		// SQL query
		// SELECT t1.id
		// FROM stock_transfert t1
		// INNER JOIN stock_transfert t2
		// ON t1.id = t2.linked_transfert_id
		// WHERE (t1.stock_transfert_message LIKE '%:filter%'
		// AND t1.warehouse_id = :warehouse_id)
		// OR (t2.stock_transfert_message LIKE '%:filter%'
		// AND t2.warehouse_id = :warehouse_id)
		$query = $this->getEntityManager()
									->createQuery('
																SELECT t1
																FROM App\Entity\StockTransfert t1
																JOIN t1.linkedTransfert t2
																WHERE (t1.stockTransfertMessage LIKE :filter
																AND t1.warehouse = :warehouse)
																OR (t2.stockTransfertMessage LIKE :filter
																AND t2.warehouse = :warehouse)
															')
									->setParameters(new ArrayCollection([
										new Parameter('warehouse', $warehouse),
										new Parameter('filter', '%'.$filter.'%')
									]));
		return $query->getResult();
	}

}
