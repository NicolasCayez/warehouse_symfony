<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\StockTransfert;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductAndQtyListType;
use App\Form\ProductAndQtyType;
use App\Form\ProductChoiceQtyType;
use App\Form\ProductCollectionFromProductReceptionType;
use App\Form\ProductCollectionFromStockTransfertType;
use App\Form\ProductCollectionFromWarehouseType;
use App\Form\ProductNewQtyType;
use App\Form\ProductToAddCollectionFromWarehouseType;
use App\Form\QtyType;
use App\Form\SelectWarehouseType;
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
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

	//* TRANSFERTS LIST For the current user
	#[Route('/transferts', name: 'transferts')]
	public function indexTransfert(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository): Response
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
			$transfertList = $stockTransfertRepository->findAll();
			foreach ($transfertList as $st) {
				if ( !($warehousesList->contains($st->getWarehouse())) ) {
					$warehousesList->removeElement($st);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
						->add('warehouse', ChoiceType::class, [
												// 'mapped' => false,
												'choices' => $user->getWarehouses(),
												'choice_label' => function (?Warehouse $w): string {
																	return $w ? ($w->getWarehouseName()) : '';
																} 
															])
						->add('submit', SubmitType::class)
						->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				return $this->redirectToRoute('transferts_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
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
					return $this->redirectToRoute('transferts_filtered', ['filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transferts');
				}
			}
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}

	//* TRANSFERTS LIST For the current user filtered
	#[Route('/transferts/filtered/', name: 'transferts_filtered')]
	public function indexTransfertFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $filter): Response
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
			$transfertList = $stockTransfertRepository->findAllFiltered($filter);
			foreach ($transfertList as $st) {
				if ( !($warehousesList->contains($st->getWarehouse())) ) {
					$warehousesList->removeElement($st);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
						->add('warehouse', ChoiceType::class, [
												// 'mapped' => false,
												'choices' => $user->getWarehouses(),
												'choice_label' => function (?Warehouse $w): string {
																	return $w ? ($w->getWarehouseName()) : '';
																} 
															])
						->add('submit', SubmitType::class)
						->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				return $this->redirectToRoute('transferts_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
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
					return $this->redirectToRoute('transferts_filtered', ['filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transferts');
				}
			}
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}

	//* TRANSFERTS LIST For the current warehouse
	#[Route('/transferts/{id}', name: 'transferts_by_warehouse')]
	public function transfertByWarehouse(Request $request, FormFactoryInterface $formFactory,  EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id): Response
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
				return $this->redirectToRoute('transferts');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of transferts for the user and warehouse
			// $transfertList = $stockTransfertRepository->findByWarehouse($warehouse);
			$transfertList = $stockTransfertRepository->findAll();
			foreach ($transfertList as $key => $st) {
				if ( $warehouse != $st->getWarehouse() && $warehouse != $st->getLinkedStockTransfert()->getWarehouse()  ) {
					unset($transfertList[$key]);
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
				return $this->redirectToRoute('transferts_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
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
					return $this->redirectToRoute('transferts_by_warehouse_filtered', ['id' => $id, 'filter' => $data['filter']]);
				}
			}
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'route_name' => $routeName,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
		]);
	}

	//* TRANSFERTS LIST For the current warehouse filtered
	#[Route('/transferts/{id}/filtered/{filter}', name: 'transferts_by_warehouse_filtered')]
	public function transfertByWarehouseFiltered(Request $request, FormFactoryInterface $formFactory,  EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id, $filter): Response
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
				return $this->redirectToRoute('transferts');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of transferts for the user and warehouse, filtered
			$transfertList = $stockTransfertRepository->findByWarehouseFiltered($warehouse, $filter);
			foreach ($transfertList as $st) {
				if ( !($warehousesList->contains($st->getWarehouse())) ) {
					$warehousesList->removeElement($st);
				}
			}
			// Form to select warehouse
			$selectWarehouseForm = $formFactory->createBuilder()
			->add('warehouse', ChoiceType::class, [
				// 'mapped' => false,
				'choices' => $user->getWarehouses(),
				'preferred_choices' => [$warehouse],
				'choice_label' => function (?Warehouse $w): string {return $w ? ($w->getWarehouseName()) : '';} 
							])
			->add('submit', SubmitType::class)
			->getForm();
			$selectWarehouseForm->handleRequest($request);
			if ($selectWarehouseForm->isSubmitted() && $selectWarehouseForm->isValid()) {
				$selectedWarehouse = $selectWarehouseForm->getData();
				dump($selectedWarehouse);
				return $this->redirectToRoute('transferts_by_warehouse', ['id' => $selectedWarehouse['warehouse']->getId(),]);
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
					return $this->redirectToRoute('transferts_by_warehouse_filtered', ['id' => $id, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transferts_by_warehouse', ['id' => $id]);
				}
			}
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,

		]);
	}


	//* NEW TRANSFERT
	#[Route('/transferts/{id}/new', name: 'new_transfert')]
	public function newTransfert(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
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
				return $this->redirectToRoute('transferts');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// list of warehouses except the actual one
			$warehouses_Destination = [];
			foreach ($warehousesList as $w) {
				if ($w != $warehouse) {
					array_push($warehouses_Destination, $w);
				}
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
					return $this->redirectToRoute('transferts_by_warehouse_filtered', ['id' => $id, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transferts_by_warehouse', ['id' => $id]);
				}
			}
			// form
			$newStockTransfert = new StockTransfert;
			$formNewTransfert = $formFactory->createBuilder()
				->add('warehouse_destination', ChoiceType::class, [
						// 'mapped' => false,
						'choices' => $warehouses_Destination,
						'choice_label' => function (?Warehouse $w): string {
											return $w ? ($w->getWarehouseName()) : '';
										} 
					])
				->add('stock_transfert_message', TextareaType::class)
				->add('submit', SubmitType::class, ['label' => 'Create',])
				->getForm();
			$formNewTransfert->handleRequest($request);
			if ($formNewTransfert->isSubmitted() && $formNewTransfert->isValid()) {
				$data = $formNewTransfert->getData();
				$dateTime = new DateTimeImmutable();
				$newStockTransfert = new StockTransfert;
				$newLinkedStockTransfert = new StockTransfert;
				// FROM
				$newStockTransfert->setWarehouse($warehouse);
				$newStockTransfert->setStockTransfertDate($dateTime);
				$newStockTransfert->setStockTransfertMessage($data['stock_transfert_message']);
				$newStockTransfert->setStockTransfertOrigin(true);
				$manager->persist($newStockTransfert);
				// TO
				$newLinkedStockTransfert->setWarehouse($data['warehouse_destination']);
				$newLinkedStockTransfert->setStockTransfertDate($dateTime);
				$newLinkedStockTransfert->setStockTransfertMessage($data['stock_transfert_message']);
				$newLinkedStockTransfert->setStockTransfertOrigin(false);
				$manager->persist($newLinkedStockTransfert);
				$newStockTransfert->setLinkedStockTransfert($newLinkedStockTransfert);
				$newLinkedStockTransfert->setLinkedStockTransfert($newStockTransfert);
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $newStockTransfert->getId()]);
			}
		}
		return $this->render('transactions/transferts/new_transfert.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'filter_form' => $filterForm,
			'form_new_transfert' => $formNewTransfert,
		]);
	}

	#[Route('/transferts/{id}/{transfertId}', name: 'transfert_detail')]
	public function transfertDetail(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, Utils $utils, UserRepository $userRepository, WarehouseRepository $warehouseRepository, InventoryRepository $inventoryRepository,
	ProductReceptionRepository $productReceptionRepository, StockModificationRepository $stockModificationRepository, StockTransfertRepository $stockTransfertRepository, $id, $transfertId): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
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
				return $this->redirectToRoute('transferts');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// get the current transfert
			$stockTransfert = $stockTransfertRepository->findOneById($transfertId);
			// All_DATA products list
			$allData = $warehouseRepository->findOneByWarehouseName('ALL_DATA');
			// form to filter list
			$filterForm = $formFactory->createBuilder()
				->add('filter', TextType::class, ['required' => false, ])
				->add('applyFilter', SubmitType::class)
				->getForm();
			$filterForm->handleRequest($request);
			if ($filterForm->isSubmitted() && $filterForm->isValid()) {
				$data = $filterForm->getData();
				if ($data['filter'] != '') {
					return $this->redirectToRoute('transfert_detail_filtered', ['id' => $id, 'transfertId' => $transfertId, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
				}
			}
			// form do update stock transfert
			$formTransfertDetail = $this->createForm(StockTransfertType::class, $stockTransfert)
				->add('warehouse_origin', WarehouseType::class, [
						'mapped' => false,
						'data' => $stockTransfert->getWarehouse(),
					])
				->add('warehouse_destination', WarehouseType::class, [
						'mapped' => false,
						'data' => $stockTransfert->getLinkedTransfert()->getWarehouse(),
					])
			;
			// form to select products to add
			$product = new Product;
			$formSelectProductToAdd = $this->createForm(QtyType::class, null, ['allow_extra_fields' => true,], $utils, $warehouse)
				->add('product', ChoiceType::class, [
						// 'mapped' => false,
						// 'label_html' => true,
						'choices' => $allData->getProducts(),
						'choice_label' => function (?Product $p) use ($utils, $warehouse) : string {
							// $html = '<p>{{ warehouse->getWarehouseName }}</p>';
							// return $p ? ($html) : ''; } 
							return $p ? ($p->getProductName() . ' | ' . $p->getBrand()->getBrandName() . ' | ' .
								'Serial : ' . $p->getProductSerialNumber() . ' | ' .
								'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
								'Actual Qty : ' . $p->getProductQuantity($utils, $warehouse) . ' | ' . 
								'Value : ' . $p->getProductValue() ) : ''; } 
					])
				->add('submit', SubmitType::class);
			$formSelectProductToAdd->handleRequest($request);
			if ($formSelectProductToAdd->isSubmitted() && $formSelectProductToAdd->isValid() ) {
				$productAndQty = $formSelectProductToAdd->getData();
				$product = $productAndQty['product'];
				$qty = $productAndQty['qty'];
				if ($qty > 0) {
					// FROM
					$newMovement = new Movement;
					$newMovement->setProduct($product);
					$newMovement->setStockTransfert($stockTransfert);
					$lastQuantity = $product->getProductQuantityByDateTime($utils, $warehouse, $stockTransfert->getStockTransfertDate());
					$newMovement->setLastQty($lastQuantity);
					$newMovement->setMovementQty(-($qty));
					$newMovement->setMovementType('STOCK_TRANSFERT');
					$manager->persist($newMovement);
					$stockTransfert->addMovement($newMovement);
					$manager->flush();
					$warehouse->addProduct($product);
					$stockTransfert->getLinkedTransfert()->getWarehouse()->addProduct($product);
				}
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			}
			// Form for all products selected in the transfert
			$formSelectedProducts = $this->createForm(ProductCollectionFromStockTransfertType::class, $stockTransfert);
			$formSelectedProducts->handleRequest($request);
			if ($formSelectedProducts->isSubmitted() && $formSelectedProducts->isValid() ) {
				$productReceptionUpdated = $formSelectedProducts->getData();
				$manager->persist($productReceptionUpdated);
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			}
		}
		return $this->render('transactions/transferts/transfert_detail.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'stock_transfert' => $stockTransfert,
			'route_name' => $routeName,
			'filter_form' => $filterForm,
			'form_transfert_detail' => $formTransfertDetail,
			'form_select_product_to_add' => $formSelectProductToAdd,
			'form_selected_products' => $formSelectedProducts,
			'utils' => $utils,
			'inventoryRepository' => $inventoryRepository,
			'productReceptionRepository' => $productReceptionRepository,
			'stockModificationRepository' => $stockModificationRepository,
			'stockTransfertRepository' => $stockTransfertRepository,
			'my_filter' => '',
		]);
	}
	
	#[Route('/transferts/{id}/{transfertId}/filtered/{filter}', name: 'transfert_detail_filtered')]
	public function transfertDetailFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, Utils $utils, UserRepository $userRepository, WarehouseRepository $warehouseRepository, InventoryRepository $inventoryRepository,
	ProductReceptionRepository $productReceptionRepository, StockModificationRepository $stockModificationRepository, StockTransfertRepository $stockTransfertRepository, $id, $transfertId, $filter): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
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
				return $this->redirectToRoute('transferts');
			}
			// list of warehouses for the user except from ALL_DATA
			$warehousesList = $user->getWarehouses();
			foreach ($warehousesList as $w) {
				if ($w->getWarehouseName() == 'ALL_DATA') {
					$warehousesList->removeElement($w);
				}
			}
			// get the current transfert
			$stockTransfert = $stockTransfertRepository->findOneById($transfertId);
			// All_DATA products list filtered
			$allData = $warehouseRepository->findOneByWarehouseName('ALL_DATA');
			foreach ($allData->getProducts() as $product) {
				if (!(str_contains($product->getProductName(), $filter))) {
					$allData->removeProduct($product);
				}
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
					return $this->redirectToRoute('transfert_detail_filtered', ['id' => $id, 'transfertId' => $transfertId, 'filter' => $data['filter']]);
				} else {
					return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
				}
			}
			// form do update stock transfert
			$formTransfertDetail = $this->createForm(StockTransfertType::class, $stockTransfert)
				->add('warehouse_origin', WarehouseType::class, [
						'mapped' => false,
						'data' => $stockTransfert->getWarehouse(),
					])
				->add('warehouse_destination', WarehouseType::class, [
						'mapped' => false,
						'data' => $stockTransfert->getLinkedTransfert()->getWarehouse(),
					])
			;
			// form to select products to add
			$product = new Product;
			$formSelectProductToAdd = $this->createForm(QtyType::class, null, ['allow_extra_fields' => true])
				->add('product', ChoiceType::class, [
						// 'mapped' => false,
						'choices' => $allData->getProducts(),
						'choice_label' => function (?Product $p): string {
											return $p ? ($p->getProductName() . ' | ' . $p->getBrand()->getBrandName() . ' | ' .
																		'Serial : ' . $p->getProductSerialNumber() . ' | ' .
																		'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
																		'Value : ' . $p->getProductValue() ) : ''; } 
					])
				->add('submit', SubmitType::class);
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
					$newMovement->setStockTransfert($stockTransfert);
					$lastquantity = $product->getProductQuantity($utils, $inventoryRepository, $productReceptionRepository, $stockModificationRepository, $stockTransfertRepository, $warehouse);
					$newMovement->setLastQty($lastquantity);
					$newMovement->setMovementQty($qty);
					$newMovement->setMovementType('STOCK_TRANSFERT');
					$manager->persist($newMovement);
					$manager->flush();
					$stockTransfert->addMovement($newMovement);
				}
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			}
			//* Form for all products selected in the transfert
			$formSelectedProducts = $this->createForm(ProductCollectionFromStockTransfertType::class, $stockTransfert);
			$formSelectedProducts->handleRequest($request);
			if ($formSelectedProducts->isSubmitted() && $formSelectedProducts->isValid() ) {
				$productReceptionUpdated = $formSelectedProducts->getData();
				$manager->persist($productReceptionUpdated);
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			}
		}
		return $this->render('transactions/transferts/transfert_detail.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'stock_transfert' => $stockTransfert,
			'route_name' => $routeName,
			'filter_form' => $filterForm,
			'form_transfert_detail' => $formTransfertDetail,
			'form_select_product_to_add' => $formSelectProductToAdd,
			'form_selected_products' => $formSelectedProducts,
			'utils' => $utils,
			'inventoryRepository' => $inventoryRepository,
			'productReceptionRepository' => $productReceptionRepository,
			'stockModificationRepository' => $stockModificationRepository,
			'stockTransfertRepository' => $stockTransfertRepository,
			'my_filter' => $filter,
		]);
	}





			//* RECEPTION DETAIL : REMOVE MOVEMENT
			#[Route('/transferts/{id}/{transfertId}/remove/{movementId}', name: 'delete_transfert_movement', methods: ['GET', 'POST'])]
			public function removeProductReceptionDetail(EntityManagerInterface $manager, MovementRepository $movementRepository, $id, $transfertId, $movementId): Response
			{
				$movementToDelete = $movementRepository->findOneById($movementId);
				$manager->remove($movementToDelete);
				$manager->flush();
				
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			}
	
	
	
	//! **************************************************
	//! TEST *********************************************
	//! **************************************************
	#[Route('/test', name: 'test')]
	public function test (Utils $utils,
												WarehouseRepository $warehouseRepository,
												ProductRepository $productRepository)
	{
		$warehouse = $warehouseRepository->findOneById(47);
		$product = $productRepository->findOneById(815);
		dump($utils->getProductQuantity($utils, $warehouse, $product));
		
		// COMMENTER POUR VERIFIER DEBUG
		return $this->redirectToRoute('');
	}
}
