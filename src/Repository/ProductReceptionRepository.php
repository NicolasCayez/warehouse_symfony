<?php

namespace App\Repository;

use App\Entity\ProductReception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductReception>
 */
class ProductReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductReception::class);
    }

    public function findAllFiltered($filter): array {
		return $this->createQueryBuilder('pr')
								->where('pr.productReceptionInvoiceRef LIKE :filter
                                        OR pr.productReceptionParcelRef LIKE :filter')
								->setParameters(new ArrayCollection([
                                    new Parameter('filter', '%'.$filter.'%')
                                ]))
								->getQuery()
								->getResult();
	}

    public function findByWarehouseFiltered($warehouse, $filter): array {
		return $this->createQueryBuilder('pr')
								->where('pr.productReceptionInvoiceRef LIKE :filter
                                        OR pr.productReceptionParcelRef LIKE :filter
                                        AND pr.warehouse = :warehouse
											')
								->setParameters(new ArrayCollection([
                                    new Parameter('warehouse', $warehouse),
                                    new Parameter('filter', '%'.$filter.'%')
                                ]))
								->getQuery()
								->getResult();
	}

}
