<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\StockTransfertRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use App\Service\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WarehouseController extends AbstractController
{
	//* ALL USER Warehouses
	#[Route('/warehouses', name: 'warehouses')]
	public function indexWarehouse(Request $request, WarehouseRepository $warehouseRepository, UserRepository $userRepository): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('warehouses/warehouses.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
		]);
	}

	//* Warehouse detail
	#[Route('/warehouses/{id}', name: 'warehouse_detail')]
	public function warehouseDetail(Request $request, StockTransfertRepository $stockTransfertRepository, WarehouseRepository $warehouseRepository, UserRepository $userRepository, Utils $utils,
      $id): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$allproductList = $warehouseRepository->findOneByWarehouseName('ALL_DATA')->getProducts();
		$warehouseProductList = [];
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			foreach ($allproductList as $one_product) {
				if ($utils->getProductQuantity($utils, $stockTransfertRepository, $warehouse, $one_product)) {
					array_push($warehouseProductList, $one_product);
				}
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('warehouses/warehouse_detail.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'warehouse_products' => $warehouseProductList,
			'stockTransfertRepository' => $stockTransfertRepository,
			'utils' => $utils,
		]);
	}
}
