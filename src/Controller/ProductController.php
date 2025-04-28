<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
	#[Route('/products', name: 'products')]
	public function indexProduct(UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductRepository $productRepository): Response
	{
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$productList = [];
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			// if ($warehouseRepository->findOneById($id)) {
			// 	$warehouse = $warehouseRepository->findOneById($id);
			// }
			$productList = $productRepository->findAll();
		}
		return $this->render('product/product.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_list' => $productList,
		]);
	}
}
