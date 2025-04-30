<?php

namespace App\DataFixtures;

use App\Entity\Cours;
use App\Entity\Langages;
use App\Entity\Niveaux;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Symfony\Component\Clock\now;

class WarehouseFixtures extends Fixture implements DependentFixtureInterface
{
	public const WAREHOUSE_REFERENCE_TAG = 'warehouse-';
	public const NB_WAREHOUSE = 4;
	private UserRepository $userRepository;
	private WarehouseRepository $warehouseRepository;

	public function __construct(UserRepository $userRepository, WarehouseRepository $warehouseRepository)
	{
		$this->userRepository = $userRepository;
		$this->warehouseRepository = $warehouseRepository;
	}

	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create('fr_FR');
		// fetching the admin
		$admin = new User;
		$users = $this->userRepository->findAll();
		foreach ($users as $user) {
			foreach ($user->getRoles() as $role){
				// dump($role);
				if ($role == 'ROLE_ADMIN') {
					$admin = $user;
				}
			}
		}
		// fake warehouse referencing all products -> used as collection when renderding forms with all the products / suppliers, etc list
		$warehouse = new Warehouse();
		$warehouse->setWarehouseName('ALL_DATA');
		$manager->persist($warehouse);
		$this->addReference(self::WAREHOUSE_REFERENCE_TAG . 0, $warehouse);
		$manager->flush();
		$lastId = $warehouse->getId();
		$lastWarehouse = $this->warehouseRepository->findOneById($lastId);
		$lastWarehouse->addUser($admin);
		$manager->flush();
		// warehouses
		for ($i = 1; $i < self::NB_WAREHOUSE; $i++) {
			$warehouse = new Warehouse();
			$warehouse->setWarehouseName($faker->lastName());
			$warehouse->setWarehousePhone($faker->phoneNumber());
			$warehouse->setWarehouseAddressNumber($faker->numberBetween(1, 150));
			$warehouse->setWarehouseAddressRoad($faker->lastName());
			$warehouse->setWarehouseAddressLabel($faker->lastName());
			$warehouse->setWarehouseAddressPostalCode($faker->numberBetween(10000, 98000));
			$warehouse->setWarehouseAddressCountry($faker->country());

			$manager->persist($warehouse);
			$this->addReference(self::WAREHOUSE_REFERENCE_TAG . $i, $warehouse);
			$manager->flush();
			$lastId = $warehouse->getId();
			$lastWarehouse = $this->warehouseRepository->findOneById($lastId);
			$lastWarehouse->addUser($admin);
		}
		$manager->flush();
	}

		public function getDependencies(): array
	{
			return [
					UserFixtures::class,
			];
	}
}

