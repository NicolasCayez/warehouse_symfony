<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyDashboardController extends AbstractController
{
	#[Route('/', name: '')]
	public function index(): Response
	{
		$showMenu = false;
		// if user autentified 
		if($this->getUser() instanceof User){
			$showMenu = true;
		}
		return $this->render('dashboard/dashboard.html.twig', [
			'showMenu' => $showMenu,
		]);
	}
	#[Route('/dashboard', name: 'dashboard')]
	public function dashboardIndex(): Response
	{
		$showMenu = false;
		// if user autentified 
		if($this->getUser() instanceof User){
			$showMenu = true;
		}
		return $this->render('dashboard/dashboard.html.twig', [
			'showMenu' => $showMenu,
		]);
	}
}
