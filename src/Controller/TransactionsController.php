<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransactionsController extends AbstractController
{
	#[Route('/transferts', name: 'transferts')]
	public function indexTransfert(): Response
	{
		return $this->render('transactions/transferts.html.twig', [
			'showMenu' => true,
		]);
	}
	#[Route('/receptions', name: 'receptions')]
	public function indexReceptions(): Response
	{
		return $this->render('transactions/receptions.html.twig', [
			'showMenu' => true,
		]);
	}
	#[Route('/stock_modifications', name: 'stock_modifications')]
	public function indexStockModifications(): Response
	{
		return $this->render('transactions/stock_modifications.html.twig', [
			'showMenu' => true,
		]);
	}

}
