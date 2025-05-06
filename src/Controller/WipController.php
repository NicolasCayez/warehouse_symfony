<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductChoiceQtyType;
use App\Form\ProductCollectionFromProductReceptionType;
use App\Form\ProductCollectionFromWarehouseType;
use App\Form\ProductNewQtyType;
use App\Form\QtyType;
use App\Repository\InventoryRepository;
use App\Repository\MovementRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\ProductRepository;
use App\Repository\StockModificationRepository;
use App\Repository\StockTransfertRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WipController extends AbstractController
{	
	#[Route('/test', name: 'test')]
	public function test (Utils $utils,
												WarehouseRepository $warehouseRepository,
												InventoryRepository $inventoryRepository,
												ProductReceptionRepository $productReceptionRepository,
												StockModificationRepository $stockModificationRepository,
												StockTransfertRepository $stockTransfertRepository,
												ProductRepository $productRepository)
	{
		$warehouse = $warehouseRepository->findOneById(47);
		$product = $productRepository->findOneById(815);
		dump($utils->getProductQuantity($utils, $inventoryRepository, $productReceptionRepository, $stockModificationRepository, $stockTransfertRepository, $warehouse, $product));
		
		// return $this->redirectToRoute('');
	}
}
