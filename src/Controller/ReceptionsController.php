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
use App\Repository\StockModificationRepository;
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

final class ReceptionsController extends AbstractController
{	
	//* **************************************************
	//* RECEPTIONS
	//* **************************************************
	//* RECEPTIONS LIST For the current user
	#[Route('/receptions', name: 'receptions')]
	public function indexReception(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of receptions for the user
			$receptionsList = $productReceptionRepository->findAll();
			foreach ($receptionsList as $pr) {
				if ( !($warehousesList->contains($pr->getWarehouse())) ) {
					$warehousesList->removeElement($pr);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
				->add('warehouse', ChoiceType::class, [
										// 'mapped' => false,
										'choices' => $user->getWarehouses(),
										'preferred_choices' => [$warehouse],
										'choice_label' => function (?Warehouse $w): string {
															return $w ? ($w->getWarehouseName()) : '';
														} 
													])
				->add('submit', SubmitType::class)
				->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				return $this->redirectToRoute('receptions_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
			}
			// form to filter list
			$filterForm = $formFactory->createBuilder()
				->add('filter', TextType::class, ['required' => false, ])
				->add('applyFilter', SubmitType::class)
				->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('receptions_filtered', ['filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('receptions');
				}
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/receptions.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'receptions_list' => $receptionsList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}
	//* RECEPTIONS LIST For the current warehouse filtered
	#[Route('/receptions/filtered/{filter}', name: 'receptions_filtered')]
	public function indexReceptionFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, $filter): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of receptions for the user, filtered
			$receptionsList = $productReceptionRepository->findAllFiltered($filter);
			foreach ($receptionsList as $pr) {
				if ( !($warehousesList->contains($pr->getWarehouse())) ) {
					$warehousesList->removeElement($pr);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
				->add('warehouse', ChoiceType::class, [
										// 'mapped' => false,
										'choices' => $user->getWarehouses(),
										'preferred_choices' => [$warehouse],
										'choice_label' => function (?Warehouse $w): string {
															return $w ? ($w->getWarehouseName()) : '';
														} 
													])
				->add('submit', SubmitType::class)
				->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				dump($selectedWarehouse);
				return $this->redirectToRoute('receptions_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
			}
			// form to filter list
			$filterForm = $formFactory->createBuilder()
				->add('filter', TextType::class, ['required' => false, ])
				->add('applyFilter', SubmitType::class)
				->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('receptions_filtered', ['filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('receptions');
				}
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/receptions.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'receptions_list' => $receptionsList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}

	//* RECEPTIONS LIST For the current warehouse
	#[Route('/receptions/{id}', name: 'receptions_by_warehouse')]
	public function ReceptionByWarehouse(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, $id): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			// get the current warehouse
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			} else {
				return $this->redirectToRoute('receptions');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of receptions for the user and warehouse
			$receptionsList = $productReceptionRepository->findByWarehouse($warehouse);
			foreach ($receptionsList as $pr) {
				if ( !($warehousesList->contains($pr->getWarehouse())) ) {
					$warehousesList->removeElement($pr);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
				->add('warehouse', ChoiceType::class, [
										// 'mapped' => false,
										'choices' => $user->getWarehouses(),
										'preferred_choices' => [$warehouse],
										'choice_label' => function (?Warehouse $w): string {
															return $w ? ($w->getWarehouseName()) : '';
														} 
													])
				->add('submit', SubmitType::class)
				->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				dump($selectedWarehouse);
				return $this->redirectToRoute('receptions_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
			}
			// form to filter list
			$filterForm = $formFactory->createBuilder()
				->add('filter', TextType::class, ['required' => false, ])
				->add('applyFilter', SubmitType::class)
				->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('receptions_by_warehouse_filtered', ['id' => $id, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('receptions_by_warehouse', ['id' => $id]);
				}
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/receptions.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'receptions_list' => $receptionsList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}
	//* RECEPTIONS LIST For the current warehouse filtered
	#[Route('/receptions/{id}/filtered/{filter}', name: 'receptions_by_warehouse_filtered')]
	public function ReceptionByWarehouseFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, $id, $filter): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			// get the current warehouse
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			} else {
				return $this->redirectToRoute('receptions');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of receptions for the user and warehouse, filtered
			$receptionsList = $productReceptionRepository->findByWarehouseFiltered($warehouse, $filter);
			foreach ($receptionsList as $pr) {
				if ( !($warehousesList->contains($pr->getWarehouse())) ) {
					$warehousesList->removeElement($pr);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
				->add('warehouse', ChoiceType::class, [
										// 'mapped' => false,
										'choices' => $user->getWarehouses(),
										'preferred_choices' => [$warehouse],
										'choice_label' => function (?Warehouse $w): string {
															return $w ? ($w->getWarehouseName()) : '';
														} 
													])
				->add('submit', SubmitType::class)
				->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				dump($selectedWarehouse);
				return $this->redirectToRoute('receptions_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
			}
			// form to filter list
			$filterForm = $formFactory->createBuilder()
				->add('filter', TextType::class, ['required' => false, ])
				->add('applyFilter', SubmitType::class)
				->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('receptions_by_warehouse_filtered', ['id' => $id, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('receptions_by_warehouse', ['id' => $id]);
				}
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/receptions.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'receptions_list' => $receptionsList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}

	//* NEW RECEPTION
	#[Route('/receptions/{id}/new', name: 'new_reception', methods: ['GET', 'POST'])]
	public function newReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductRepository $productRepository, $id,): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
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
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/reception_detail.html.twig', [
			'route_name' => $routeName,
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
		$routeName = $request->attributes->get('_route');
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
			// get the user
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
					return $this->redirectToRoute('reception_detail_filtered', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => $data['filter']]);
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
				$newMovement->setMovementQty($qty);
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
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/reception_detail.html.twig', [
			'route_name' => $routeName,
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
	#[Route('/receptions/{id}/{productReceptionId}/remove/{movementId}', name: 'delete_reception_movement', methods: ['GET', 'POST'])]
	public function removeProductReceptionDetail(EntityManagerInterface $manager, MovementRepository $movementRepository, $id, $productReceptionId, $movementId): Response
	{
		if($this->getUser() instanceof User){
			$movementToDelete = $movementRepository->findOneById($movementId);
			$manager->remove($movementToDelete);
			$manager->flush();
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->redirectToRoute('reception_detail', ['id' => $id, 'productReceptionId' => $productReceptionId]);
	}
	//* NEW RECEPTION DETAIL : EDIT AND ADD PRODUCTS
	#[Route('/receptions/{id}/{productReceptionId}/filter/{filter}', name: 'reception_detail_filtered')]
	public function newReceptionDetailFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository, ProductRepository $productRepository, $id, $productReceptionId, $filter): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		$allProductList = [];
		$productList = [];
		$filterForm = null;
		$productReception = new ProductReception;
		// if user autentified 
		if($this->getUser() instanceof User){
			$userAuthentified = true;
			// get the user
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
			$newMovement->setMovementQty($productWithQty['new_qty']);
			$manager->persist($newMovement);
			$manager->flush();
			return $this->redirectToRoute('reception_detail_filter', ['id' => $id, 'productReceptionId' => $productReceptionId, 'filter' => $filter]);
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/receptions/reception_detail.html.twig', [
			'route_name' => $routeName,
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

}
