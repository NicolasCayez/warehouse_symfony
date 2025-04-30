<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\ProductMovement;
use App\Entity\ProductReception;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductReceptionType;
use App\Repository\MovementRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransactionsController extends AbstractController
{	
	//* **************************************************
	//* TRANSFERTS
	//* **************************************************
	#[Route('/transferts', name: 'transferts')]
	public function indexTransfert(): Response
	{
		return $this->render('transactions/transferts/transferts.html.twig', [
			'showMenu' => true,
		]);
	}

	//* **************************************************
	//* RECEPTIONS
	//* **************************************************

	//* RECEPTIONS LIST For the current warehouse
	#[Route('/receptions/{id}', name: 'receptions')]
	public function indexReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, $id): Response
	{
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
			$receptionsList = $productReceptionRepository->findByWarehouse($id);
		}
		return $this->render('transactions/receptions/receptions.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'receptions_list' => $receptionsList,
		]);
	}

	//* NEW RECEPTION
	#[Route('/receptions/{id}/new', name: 'new_reception')]
	public function newReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductRepository $productRepository, $id,): Response
	{
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// $productList = [];
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			// $productList = $productRepository->findAll();
		}
		// Form
		$productReception = new ProductReception;
		$form = $this->createForm(ProductReceptionType::class, $productReception);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid() ) {
			$productReception = $form->getData();
			$productReception->setWarehouse($warehouse);
			$manager->persist($productReception);
			$manager->flush();
			$productReceptionId = $productReception->getId();
			return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => '!']);
		}
		return $this->render('transactions/receptions/new_reception.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_list' => 'empty',
			'form' => $form,
		]);
	}
	//* NEW RECEPTION DETAIL : EDIT AND ADD PRODUCTS
	#[Route('/receptions/{id}/{productReceptionId}', name: 'reception_detail')]
	public function newReceptionDetail(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, MovementRepository $movementRepository, ProductReceptionRepository $productReceptionRepository, ProductRepository $productRepository, $id, $productReceptionId): Response
	{
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$productList = [];
		$allProductList = [];
		$filterForm = null;
		$productReception = new ProductReception;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$allProductList = $productRepository->findAll();
						//filter product list
			$filterForm = $formFactory->createBuilder()
						->add('filter', TextType::class, ['required' => false, ])
						->add('applyFilter', SubmitType::class)
						->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('reception_detail_filter', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => $data['filter']]);
				}
			}
			$productReception = $productReceptionRepository->findOneById($productReceptionId);
		}
		$products = [];
		foreach ($allProductList as $product) {
			if (!($warehouse->getProducts()->contains($product))) {
				array_push($products, $product);
			}
		}
		// ! ****************************
		//! Refaire le formulaire depuis warehouse all data pour avoir les produits
		// ! ****************************
		//* Form for all products NOT selected in the reception
		$formSelectProductToAdd = $formFactory->createBuilder()
					->add('products', ChoiceType::class, [
						'choices' => $products,
						'choice_label' => function (?Product $p): string {
								return $p ? ($p->getProductName() . ' | ' . 
															$p->getBrand()->getBrandName() . ' | ' .
															'Serial : ' . $p->getProductSerialNumber() . ' | ' .
															'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
															'Value : ' . $p->getProductValue()
														) : '';
							} 
						])
					->add('submit', SubmitType::class)
					->getForm();
		if ($formSelectProductToAdd->isSubmitted() && $formSelectProductToAdd->isValid() ) {
			$productWithQty = $formSelectProductToAdd->getData();
			$newMovement = new Movement;
			$newMovement->setProduct($productWithQty['product']);
			$newMovement->setNewQty($productWithQty['newQty']);
			$manager->persist($newMovement);
			$manager->flush();
			return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
		}
		//* Form for all products selected in the reception
		$movement = $movementRepository->findOneById($productReceptionId);
		$formSelectedProducts = $movement;
		// $formSelectedProducts = $this->createForm(MovementDetailType::class, $movement);
		// $formSelectedProducts->handleRequest($request);
		// if ($formSelectedProducts->isSubmitted() && $formSelectedProducts->isValid() ) {
		// 	$productReceptionUpdated = $formSelectedProducts->getData();
		// 	$manager->persist($productReceptionUpdated);
		// 	$manager->flush();
		// 	return $this->redirectToRoute('new_reception_detail', ['id' => $id, 'mvmtId' => $mvmtId]);
		// }

		return $this->render('transactions/receptions/new_reception.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_reception' => $productReception,
			'product_list' => $productList,
			'filter_form' => $filterForm,
			'form_select_product_to_add' => $formSelectProductToAdd,
			'form_selected_products' => $formSelectedProducts,
		]);
	}
	//* NEW RECEPTION DETAIL : EDIT AND ADD PRODUCTS
	#[Route('/receptions/{id}/{productReceptionId}/{filter}', name: 'reception_detail_filter')]
	public function newReceptionDetailFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, ProductRepository $productRepository, $id, $productReceptionId, $filter): Response
	{
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$productList = [];
		$filterForm = null;
		$productReception = new ProductReception;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// List of warehouses for the user
			$user = $userRepository->findOneById($this->getUser());
			$warehousesList = $user->getWarehouses();
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$allProductList = $productRepository->findAllFiltered($filter);
						//filter product list
			$filterForm = $formFactory->createBuilder()
						->add('filter', TextType::class, ['required' => false, ])
						->add('applyFilter', SubmitType::class)
						->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] == '') {
					return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
				}
				return $this->redirectToRoute('reception_detail_filter', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => $data['filter']]);
			}
			$productReception = $productReceptionRepository->findOneById($productReceptionId);
		}
		//* Form all products IN stock

		//* Form all products NOT IN stock
		// adding products if not in stock
		$productList = new ArrayCollection(['products' => []]);
		$products = [];
		foreach ($allProductList as $product) {
			if (!($warehouse->getProducts()->contains($product))) {
				array_push($products, $product);
			}
		}
		$formSelectProductToAdd = $formFactory->createBuilder()
					->add('products', ChoiceType::class, [
						'choices' => $products,
						'choice_label' => function (?Product $p): string {
								return $p ? ($p->getProductName() . ' | ' . 
															$p->getBrand()->getBrandName() . ' | ' .
															'Serial : ' . $p->getProductSerialNumber() . ' | ' .
															'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
															'Value : ' . $p->getProductValue()
														) : '';
							} 
						])
					->getForm();
		if ($formSelectProductToAdd->isSubmitted() && $formSelectProductToAdd->isValid() ) {
			$productWithQty = $formSelectProductToAdd->getData();
			$newMovement = new Movement;
			$newMovement->setProduct($productWithQty['product']);
			$newMovement->setNewQty($productWithQty['new_qty']);
			$manager->persist($newMovement);
			$manager->flush();
			return $this->redirectToRoute('reception_detail_filter', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => $filter]);
		}
		return $this->render('transactions/receptions/new_reception.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_reception' => $productReception,
			'product_list' => $productList,
			'filter_form' => $filterForm,
			'form_select_product_to_add' => $formSelectProductToAdd,
			// 'form_selected_products' => $formSelectedProducts,
		]);
	}

	//* **************************************************
	//* STOCK MODIFICATIONS
	//* **************************************************
	#[Route('/stock_modifications', name: 'stock_modifications')]
	public function indexStockModifications(): Response
	{
		return $this->render('transactions/stock_modifications/stock_modifications.html.twig', [
			'showMenu' => true,
		]);
	}

}
