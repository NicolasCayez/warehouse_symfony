<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;

final class ProductController extends AbstractController
{
	#[Route('/products', name: 'products')]
	public function indexProduct(Request $request, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductRepository $productRepository, FormFactoryInterface $formFactory): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$productList = [];
		$data = ['empty'];
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			$productList = $productRepository->findAll();
			//filter product list
			$filterForm = $formFactory->createBuilder()
																->add('filter', TextType::class)
																->add('applyFilter', SubmitType::class)
																->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				return $this->redirectToRoute('products_filtered', ['filter' => $data['filter']]);
			}
		}
		return $this->render('products/products.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_list' => $productList,
			'filter_form' => $filterForm,
		]);
	}
	#[Route('/products/{filter}', name: 'products_filtered')]
	public function indexProductFiltered(Request $request, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductRepository $productRepository, FormFactoryInterface $formFactory, $filter): Response
	{
		$routeName = $request->attributes->get('_route');
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
			$productList = $productRepository->findAllFiltered($filter);
			$filterForm = $formFactory->createBuilder()
			->add('filter', TextType::class)
			->add('applyFilter', SubmitType::class)
			->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
			$data = $filterForm->getData();
			return $this->redirectToRoute('products_filtered', ['filter' => $data['filter']]);
			}
		}
		return $this->render('products/products.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_list' => $productList,
			'filter_form' => $filterForm,
		]);
	}
}
