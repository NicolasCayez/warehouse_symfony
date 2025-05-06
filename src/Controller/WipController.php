<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\StockTransfert;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductChoiceQtyType;
use App\Form\ProductCollectionFromProductReceptionType;
use App\Form\ProductCollectionFromWarehouseType;
use App\Form\ProductNewQtyType;
use App\Form\QtyType;
use App\Form\StockTransfertDetailType;
use App\Form\StockTransfertType;
use App\Form\WarehouseType;
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
	//* **************************************************
	//* TRANSFERTS
	//* **************************************************

	#[Route('/transferts', name: 'transferts')]
	public function indexTransfert(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			// if ($warehouseRepository->findOneById($id)) {
			// 	$warehouse = $warehouseRepository->findOneById($id);
			// }
			$transfertList = $stockTransfertRepository->findAll();
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'route_name' => $routeName,
		]);
	}



	#[Route('/transferts/{id}', name: 'transfert_detail')]
	public function transfertByWarehouse(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$transfertList = $stockTransfertRepository->findByWarehouse($warehouse);
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'route_name' => $routeName,
		]);
	}



	#[Route('/transferts/{id}/new', name: 'new_transfert')]
	public function newTransfert(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$warehouses_Destination = [];
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() != 'ALL_DATA' && $w != $warehouse) {
					array_push($warehouses_Destination, $w);
				}
			}
			$transfertList = $stockTransfertRepository->findByWarehouse($warehouse);
			// form
			$newStockTransfert = new StockTransfert;
			$formNewTransfert = $this->createForm(StockTransfertType::class, $newStockTransfert)
														->add('warehouse_origin', WarehouseType::class, [
															'mapped' => false,
															'data' => $warehouse,
														])
														->add('warehouses_destination', ChoiceType::class, [
															'mapped' => false,
															'choices' => $warehouses_Destination,
															'choice_label' => function (?Warehouse $w): string {
																				return $w ? ($w->getWarehouseName()) : '';
																			} 
														])
			;
		}
		return $this->render('transactions/transferts/new_transfert.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'route_name' => $routeName,
			'form_new_transfert' => $formNewTransfert,
		]);
	}




	#[Route('/transferts/{id}/{transfertId}', name: 'transfert_detail')]
	public function transfertDetail(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id, $transfertId): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$stockTransfert = $stockTransfertRepository->findOneById($transfertId);

			// form
			$newStockTransfert = new StockTransfert;
			$formNewTransfertDetail = $this->createForm(StockTransfertType::class, $newStockTransfert)
														->add('warehouse_origin', WarehouseType::class, [
															'mapped' => false,
															'data' => $stockTransfert->getWarehouse(),
															// 'data' => $warehouse,
														])
														->add('warehouse_destination', WarehouseType::class, [
															'mapped' => false,
															'data' => $stockTransfert->getLinkedTransfert()->getWarehouse(),
															// 'data' => $warehouse,
														])
			;



		}
		return $this->render('transactions/transferts/transfert_detail.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'stock_transfert' => $stockTransfert,
			'route_name' => $routeName,
			'form_new_transfert_detail' => $formNewTransfertDetail,
		]);
	}
	
	
	
	
	
	//! **************************************************
	//! TEST *********************************************
	//! **************************************************
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
		
		// COMMENTER POUR VERIFIER DEBUG
		return $this->redirectToRoute('');
	}
}
