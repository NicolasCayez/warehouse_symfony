<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\ProductReception;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductCollectionFromProductReceptionType;
use App\Form\ProductCollectionFromWarehouseType;
use App\Form\ProductReceptionType;
use App\Form\QtyType;
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
	//* TRANSACTIONS
	//* **************************************************
	#[Route('/Transactions', name: 'transactions')]
	public function indexTransactions(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository): Response
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
			// if ($warehouseRepository->findOneById($id)) {
			// 	$warehouse = $warehouseRepository->findOneById($id);
			// }
			// $receptionsList = $productReceptionRepository->findByWarehouse($id);
		}
		return $this->render('transactions/transactions.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			// 'receptions_list' => $receptionsList,
		]);
	}

	//* **************************************************
	//* TRANSFERTS
	//* **************************************************

	//* **************************************************
	//* RECEPTIONS
	//* **************************************************

	//* RECEPTIONS LIST For the current warehouse
	#[Route('/receptions/{id}', name: 'receptions')]
	public function indexReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, $id): Response
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
	#[Route('/receptions/{id}/new', name: 'new_reception', methods: ['GET', 'POST'])]
	public function newReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductRepository $productRepository, $id,): Response
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
		return $this->render('transactions/receptions/reception_detail.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'product_list' => 'empty',
			'form' => $form,
		]);
	}
		//* RECEPTION DETAIL : EDIT AND ADD PRODUCTS
		#[Route('/receptions/{id}/{productReceptionId}', name: 'reception_detail', methods: ['GET', 'POST'])]
		public function newReceptionDetail(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, MovementRepository $movementRepository, ProductReceptionRepository $productReceptionRepository, ProductRepository $productRepository, $id, $productReceptionId): Response
		{
			$userAuthentified = false;
			$warehousesList = [];
			$warehouse = New Warehouse;
			$allData = new Warehouse;
			$productList = [];
			$filterForm = null;
			$productReception = $productReceptionRepository->findOneById($productReceptionId);
			// if user autentified 
			if($this->getUser() instanceof User){
				$userAuthentified = true;
				// List of warehouses for the user
				$user = $userRepository->findOneById($this->getUser());
				$warehousesList = $user->getWarehouses();
				if ($warehouseRepository->findOneById($id)) {
					$warehouse = $warehouseRepository->findOneById($id);
				}
				// All products list
				$allData = $warehouseRepository->findOneByWarehouseName('ALL_DATA');
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
			}
			// $formSelectProductToAdd = $this->createForm(ProductCollectionFromWarehouseType::class, $allData);
			$product = new Product;
			$formSelectProductToAdd = $this->createForm(QtyType::class, null, ['allow_extra_fields' => true])
						->add('product', ChoiceType::class, [
												// 'mapped' => false,
												'choices' => $allData->getProducts(),
												'choice_label' => function (?Product $p): string {
																	return $p ? ($p->getProductName() . ' | ' . 
																								$p->getBrand()->getBrandName() . ' | ' .
																								'Serial : ' . $p->getProductSerialNumber() . ' | ' .
																								'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
																								'Value : ' . $p->getProductValue()
																							) : '';
																} 
											])
						->add('add_product', SubmitType::class);
			$formSelectProductToAdd->handleRequest($request);
			if ($formSelectProductToAdd->isSubmitted() && $formSelectProductToAdd->isValid() ) {
				$productAndQty = $formSelectProductToAdd->getData();
				$product = $productAndQty['product'];
				$qty = $productAndQty['qty'];
				if ($qty != 0) {
					dump($product);
					dump($qty);
					$newMovement = new Movement;
					$newMovement->setProduct($product);
					$newMovement->setProductReception($productReception);
					$newMovement->setLastQty(0); //! A MODIFIER AVEC QTE EN STOCK
					$newMovement->setNewQty($qty);
					$newMovement->setMovementType('PRODUCT_RECEPTION');
					$manager->persist($newMovement);
					$manager->flush();
					$productReception->addMovement($newMovement);
				}
				$manager->flush();
				return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
			}
			//* Form for all products selected in the reception
			$formSelectedProducts = $this->createForm(ProductCollectionFromProductReceptionType::class, $productReception);
			$formSelectedProducts->handleRequest($request);
			if ($formSelectedProducts->isSubmitted() && $formSelectedProducts->isValid() ) {
				$productReceptionUpdated = $formSelectedProducts->getData();
				// unset($formSelectedProducts);
				// $formSelectedProducts = $this->createForm(ProductCollectionFromProductReceptionType::class, $productReception);
				$manager->persist($productReceptionUpdated);
				$manager->flush();
				return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
			}
			return $this->render('transactions/receptions/reception_detail.html.twig', [
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
	
	
		//* RECEPTION DETAIL : REMOVE MOVEMENT
		#[Route('/receptions/{id}/{productReceptionId}/remove/{movementId}', name: 'delete_movement', methods: ['GET', 'POST'])]
		public function removeProductReceptionDetail(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, MovementRepository $movementRepository, ProductReceptionRepository $productReceptionRepository, ProductRepository $productRepository, $id, $productReceptionId, $movementId): Response
		{
			$movementToDelete = $movementRepository->findOneById($movementId);
			$manager->remove($movementToDelete);
			$manager->flush();
			
			return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
		}
		// ! FAIRE AVEC FILTRE
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
		return $this->render('transactions/receptions/reception_detail.html.twig', [
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
