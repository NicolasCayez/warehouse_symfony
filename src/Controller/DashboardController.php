<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
	// DEFAULT ROUTE
	#[Route('/', name: '')]
	public function index(): Response
	{
		return $this->redirectToRoute('dashboard', [
			'id' => 'default',
		]);
	}
	// DEFAULT dashboard no parameters
	#[Route('/dashboard', name: 'dashboard_default')]
	public function dashboardDefault(): Response
	{
		return $this->redirectToRoute('dashboard', [
			'id' => 'default',
		]);
	}
	// ROUTE : dashboard with selected warehouse
	#[Route('/dashboard/{id}', name: 'dashboard')]
	public function dashboard(WarehouseRepository $warehouseRepository, UserRepository $userRepository, $id): Response
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
		}
		return $this->render('dashboard/dashboard.html.twig', [
			'user_authentified' => $userAuthentified,
			'user_warehouses' => $warehousesList,
			'warehouse' => $warehouse,
		]);
	}
}
