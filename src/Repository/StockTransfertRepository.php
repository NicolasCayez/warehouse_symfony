<?php

namespace App\Repository;

use App\Entity\StockTransfert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

	public function findByWarehouseFiltered($warehouse, $filter): array {
		return $this->createQueryBuilder('t')
								->where('t.stockTransfertMessage LIKE :filter
                                        AND t.warehouse = :warehouse
											')
								->setParameters(new ArrayCollection([
                                    new Parameter('warehouse', $warehouse),
                                    new Parameter('filter', '%'.$filter.'%')
                                ]))
								->getQuery()
								->getResult();
	}

}
