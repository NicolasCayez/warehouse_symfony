<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WarehouseController extends AbstractController
{
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
		}
		return $this->render('warehouses/warehouses.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
		]);
	}
	#[Route('/warehouses/{id}', name: 'warehouse_detail')]
	public function warehouseDetail(Request $request, WarehouseRepository $warehouseRepository, UserRepository $userRepository, $id): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
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
		}
		return $this->render('warehouses/warehouse_detail.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
		]);
	}
}
