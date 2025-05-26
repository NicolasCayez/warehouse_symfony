<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Entity\StockTransfert;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductCollectionFromStockTransfertType;
use App\Form\QtyType;
use App\Form\StockTransfertType;
use App\Form\WarehouseType;
use App\Repository\InventoryRepository;
use App\Repository\MovementRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\StockModificationRepository;
use App\Repository\StockTransfertRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

final class StockTransfertController extends AbstractController
{	
	/** Route : transferts
  * Displays the transferts list for the user, filtered
  * @Param string $filter (optional)
  */
	#[
		Route('/transferts_list', name: 'transferts', methods: ['GET']),
		Route('/transferts_list/{filter}', name: 'transferts', methods: ['GET', 'POST']),
	]
	public function indexTransfertFiltered(Request $request, FormFactoryInterface $formFactory, UserRepository $userRepository, StockTransfertRepository $stockTransfertRepository, ?string $filter = null): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User) {
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
				$userAuthentified = true;
				// list of warehouses for the user except from ALL_DATA
				$warehousesList = $user->getWarehouses();
				foreach ($warehousesList as $w) {
					if ($w->getWarehouseName() == 'ALL_DATA') {
						$warehousesList->removeElement($w);
					}
				}
				// list of transferts for the user, filtered
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
					->add('filter', TextType::class, [
							'required' => false,
							'constraints' => [
									new Regex('/^[a-zA-ZÀ-ÖØ-öø-ÿ0-9]+$/'),
								]
						])
					->add('applyFilter', SubmitType::class)
					->getForm();
				$filterForm->handleRequest($request);
				if ($filterForm->isSubmitted() && $filterForm->isValid()) {
					$data = $filterForm->getData();
					if ($data['filter'] != '') {
						return $this->redirectToRoute('transferts', ['filter' => $data['filter']]);
					} else {
						return $this->redirectToRoute('transferts');
					}
				}
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
			'stock_transfert_repository' => $stockTransfertRepository,
		]);
	}

	/** Route : transferts_by_warehouse
  * Displays the transferts list for the user and warehouse, filtered
  * @Param integer $id - The warehouse id
  * @Param string $filter (optional)
  */
	#[
		Route('/transferts/{id}/list', name: 'transferts_by_warehouse', methods: ['GET', 'POST']),
		Route('/transferts/{id}/list/{filter}', name: 'transferts_by_warehouse', methods: ['GET', 'POST'])
	]
	public function transfertByWarehouseFiltered(Request $request, FormFactoryInterface $formFactory, UserRepository $userRepository, WarehouseRepository $warehouseRepository, StockTransfertRepository $stockTransfertRepository, $id, ?string $filter=null): Response
	{
		$routeName = $request->attributes->get('_route');
		$userAuthentified = false;
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User) {
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
				$userAuthentified = true;
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
					if ( !($warehousesList->contains($st->getWarehouse()) || $warehousesList->contains($st->getLinkedStockTransfert()->getWarehouse()) )) {
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
					->add('filter', TextType::class, [
							'required' => false,
							'constraints' => [
									new Regex('/^[a-zA-Z0-9_.-]+$/'),
								]
						])
					->add('applyFilter', SubmitType::class)
					->getForm();
				$filterForm->handleRequest($request);
				if ($filterForm->isSubmitted() && $filterForm->isValid()) {
					$data = $filterForm->getData();
					if ($data['filter'] != '') {
						return $this->redirectToRoute('transferts_by_warehouse', ['id' => $id, 'filter' => $data['filter']]);
					} else {
						return $this->redirectToRoute('transferts_by_warehouse', ['id' => $id]);
					}
				}
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/transferts/transferts.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'transfert_list' => $transfertList,
			'filter_form' => $filterForm,
			'select_warehouse_form' => $selectWarehouseForm,
			'stock_transfert_repository' => $stockTransfertRepository,
		]);
	}

	/** Route : new_transfert
  * To create a new transfert for the warehouse
  * @Param integer $id - The warehouse id
  */
	#[Route('/transferts/{id}/new', name: 'new_transfert', methods: ['GET', 'POST'])]
	public function newTransfert(Request $request, Utils $utils, FormFactoryInterface $formFactory, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, $id): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
	if($this->getUser() instanceof User) {
		// get the user
		$user = $userRepository->findOneById($this->getUser());
		if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
			$userAuthentified = true;
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
				$warehousesDestination = [];
				foreach ($warehousesList as $w) {
					if ($w != $warehouse) {
						array_push($warehousesDestination, $w);
					}
				}
				// form
				$newStockTransfert = new StockTransfert;
				$formNewTransfert = $formFactory->createBuilder()
					->add('warehouse_destination', ChoiceType::class, [
							// 'mapped' => false,
							'choices' => $warehousesDestination,
							'choice_label' => function (?Warehouse $w): string {
												return $w ? ($w->getWarehouseName()) : '';
											} 
						])
					->add('stock_transfert_message', TextareaType::class, [
						'constraints' => [
							new Length([
								'min' => 3,
								'minMessage' => 'You must write at least {{ limit }} characters',
								'max' => 255,
								'maxMessage' => 'You must write at most {{ limit }} characters',
							]),
							new Regex(
								'/^[0-9A-Za-zÀ-ÖØ-öø-ÿ\'\"()&.\-_\s]+$/', 'You should write letters, numbers, or " \' _ - . &'
							),
						]
					])
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
	$newStockTransfert->setStockTransfertMessage($utils->cleanInputStatic($data['stock_transfert_message']));
	$newStockTransfert->setStockTransfertOrigin(true);
	$manager->persist($newStockTransfert);
	// TO
	$newLinkedStockTransfert->setWarehouse($data['warehouse_destination']);
	$newLinkedStockTransfert->setStockTransfertDate($dateTime);
	$newLinkedStockTransfert->setStockTransfertMessage($utils->cleanInputStatic($data['stock_transfert_message']));
	$newLinkedStockTransfert->setStockTransfertOrigin(false);
	$manager->persist($newLinkedStockTransfert);
	$manager->flush();
	$newStockTransfert->setLinkedStockTransfert($newLinkedStockTransfert);
	$newLinkedStockTransfert->setLinkedStockTransfert($newStockTransfert);
	// $newStockTransfert->setLinkedStockTransfertId($newLinkedStockTransfert->getId());
	// $newLinkedStockTransfert->setLinkedStockTransfertId($newStockTransfert->getId());
	$manager->flush();
	return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $newStockTransfert->getId()]);
}
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/transferts/new_transfert.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'form_new_transfert' => $formNewTransfert,
		]);
	}

	/** Route : transfert_detail
  * To edit a transfert detail : add or edit movements (product and quantity)
  * @Param integer $id - The warehouse id
  * @Param integer $transfertId - The transfert id
  * @Param string $filter (optional)
  */
	#[
		Route('/transferts/{id}/detail/{transfertId}', name: 'transfert_detail', methods: ['GET', 'POST']),
		Route('/transferts/{id}/detail/{transfertId}/{filter}', name: 'transfert_detail', methods: ['GET', 'POST'])
	]
	public function transfertDetailFiltered(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, Utils $utils, UserRepository $userRepository, WarehouseRepository $warehouseRepository, InventoryRepository $inventoryRepository,
	ProductReceptionRepository $productReceptionRepository, StockModificationRepository $stockModificationRepository, StockTransfertRepository $stockTransfertRepository, $id, $transfertId, ?string $filter=null): Response
	{
		$userAuthentified = false;
		$routeName = $request->attributes->get('_route');
		$warehousesList = [];
		$warehouse = New Warehouse;
		// if user autentified 
		if($this->getUser() instanceof User) {
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
				$userAuthentified = true;
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
				dump($stockTransfert);
				// All_DATA products list filtered
				$allData = $warehouseRepository->findOneByWarehouseName('ALL_DATA');
				foreach ($allData->getProducts() as $product) {
					if (!(str_contains($product->getProductName(), $filter))) {
						$allData->removeProduct($product);
					}
				}
				// form to filter list
				$filterForm = $formFactory->createBuilder()
					->add('filter', TextType::class, [
							'required' => false,
							'constraints' => [
									new Regex('/^[a-zA-Z0-9_.-]+$/'),
								]
						])
					->add('applyFilter', SubmitType::class)
					->getForm();
				$filterForm->handleRequest($request);
				if ($filterForm->isSubmitted() && $filterForm->isValid()) {
					$data = $filterForm->getData();
					if ($data['filter'] != '') {
						return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId, 'filter' => $data['filter']]);
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
				// Removing products already in the stock transfert
				$allDataNoDuplicate = $allData;
				foreach ($stockTransfert->getMovements() as $mvmt) {
					if ($allData->getProducts()->contains($mvmt->getProduct())) {
						$allData->getProducts()->removeElement($mvmt->getProduct());
					}
				}
$formSelectProductToAdd = $this->createForm(QtyType::class, null, ['allow_extra_fields' => true])
	->add('product', ChoiceType::class, [
			// 'mapped' => false,
			'choices' => $allDataNoDuplicate->getProducts(),
			'choice_label' => function (?Product $p) use ($utils, $stockTransfertRepository, $warehouse) : string {
								return $p ? ($p->getProductName() . ' | ' . $p->getBrand()->getBrandName() . ' | ' .
															'Serial : ' . $p->getProductSerialNumber() . ' | ' .
															'Ref : ' . $p->getProductRef() . ' / ' . $p->getProductRef() . ' | ' .
															'Actual Qty : ' . $p->getProductQuantity($utils, $stockTransfertRepository, $warehouse) . ' | ' .
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
						$lastquantity = $product->getProductQuantity($utils, $stockTransfertRepository, $warehouse);
						$newMovement->setLastQty($lastquantity);
						$newMovement->setMovementQty($qty);
						$newMovement->setMovementType('STOCK_TRANSFERT');
						$manager->persist($newMovement);
						$manager->flush();
						$warehouse->addProduct($product);
						$manager->flush();
						$stockTransfert->getLinkedTransfert()->getWarehouse()->addProduct($product);
						$manager->flush();
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
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
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

	/** Route : delete_transfert_movement
  * To delete a movement linked to a transfert
  * @Param integer $id - The warehouse id
  * @Param integer $transfertId - The transfert id
  * @Param integer $movementId - The movement id
  */
	#[Route('/transferts/{id}/detail/{transfertId}/remove/{movementId}', name: 'delete_transfert_movement', methods: ['GET', 'POST'])]
	public function removeProductReceptionDetail(EntityManagerInterface $manager, UserRepository $userRepository, MovementRepository $movementRepository, $id, $transfertId, $movementId): Response
	{
				//if user autentified 
		if($this->getUser() instanceof User) {
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
				$movementToDelete = $movementRepository->findOneById($movementId);
				$manager->remove($movementToDelete);
				$manager->flush();
				return $this->redirectToRoute('transfert_detail', ['id' => $id, 'transfertId' => $transfertId]);
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
	}

		/** Route : delete_transfert
  * To edit a transfert detail : add or edit movements (one product and quantity)
  * @Param integer $id - The warehouse id
  * @Param integer $transfertId - The transfert id
  */
	#[Route('/transferts/{id}/remove/{transfertId}', name: 'delete_transfert', methods: ['GET', 'POST'])]
	public function deleteTransfert(EntityManagerInterface $manager, Utils $utils, UserRepository $userRepository, StockTransfertRepository $stockTransfertRepository, $id, $transfertId): Response
	{
		//if user autentified 
		if($this->getUser() instanceof User) {
			// get the user
			$user = $userRepository->findOneById($this->getUser());
			if (array_search('ROLE_MANAGER', $user->getRoles()) || array_search('ROLE_ADMIN', $user->getRoles())){
				//User allowed, proceeding wit the delete action
//get the current transfert
$stockTransfert = $stockTransfertRepository->findOneById($transfertId);
// $linkedStockTransfert = $stockTransfertRepository->findOneById($stockTransfert->getLinkedStockTransfertId());
$linkedStockTransfert = $stockTransfert->getLinkedStockTransfert();
$conn = $manager->getConnection();
// remove aull linked movements
$sql = '
				UPDATE stock_transfert
				SET linked_transfert_id = NULL
				where id = :id
				or id = :linkedId
				';
$conn->executeQuery($sql, ['id' => $transfertId, 'linkedId' => $linkedStockTransfert->getId()]);
$manager->remove($stockTransfert);
$manager->flush();
$manager->remove($linkedStockTransfert);
$manager->flush();
				return $this->redirectToRoute('transferts', ['id' => $id]);
			} else {
				// User not allowed
				return $this->redirectToRoute('');
			}
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
	}
}
