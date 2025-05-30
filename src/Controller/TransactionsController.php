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

final class TransactionsController extends AbstractController
{	
	//* **************************************************
	//* TRANSACTIONS
	//* **************************************************
	#[Route('/transactions', name: 'transactions')]
	public function indexTransactions(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, WarehouseRepository $warehouseRepository, ProductReceptionRepository $productReceptionRepository): Response
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
		} else {
			// User not identified
			return $this->redirectToRoute('');
		}
		return $this->render('transactions/transactions.html.twig', [
			'route_name' => $routeName,
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
		]);
	}
}
