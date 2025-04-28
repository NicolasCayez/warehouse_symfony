<?php

namespace App\Controller;

use App\Entity\ProductReception;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Form\ProductReceptionFormType;
use App\Form\ProductReceptionType;
use App\Repository\MovementRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
		#[Route('/receptions/new/{id}', name: 'new_reception')]
		public function newReception(Request $request, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, ProductRepository $productRepository, $id,): Response
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
				if ($warehouseRepository->findOneById($id)) {
					$warehouse = $warehouseRepository->findOneById($id);
				}
				$productList = $productRepository->findAll();
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
				$mvmtId = $productReception->getId();
				return $this->redirectToRoute('new_reception_detail', ['id' => $id, 'mvmtId' => $mvmtId]);
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
	#[Route('/receptions/new/{id}/{mvmtId}', name: 'new_reception_detail')]
	public function newReceptionDetail(Request $request, EntityManagerInterface $manager, UserRepository $userRepository ,WarehouseRepository $warehouseRepository, MovementRepository $movementRepository, ProductRepository $productRepository, $id, $mvmtId): Response
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
			if ($warehouseRepository->findOneById($id)) {
				$warehouse = $warehouseRepository->findOneById($id);
			}
			$productList = $productRepository->findAll();
			$movement = $movementRepository->findOneById($mvmtId);
		}
		// Form
		$productReception = new ProductReception;
		// $form = $this->createForm(ProductReceptionType::class, $productReception);
		// $form->handleRequest($request);
		// if ($form->isSubmitted() && $form->isValid() ) {
		// 	$productReception = $form->getData();
		// 	$manager->persist($productReception);
		// 	$manager->flush();
		// }
		return $this->render('transactions/receptions/new_reception.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
			'movement' => $movement,
			'product_list' => $productList,
			// 'form' => $form,
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
