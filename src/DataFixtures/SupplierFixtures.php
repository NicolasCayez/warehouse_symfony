<?php

namespace App\DataFixtures;

use App\Entity\Cours;
use App\Entity\Langages;
use App\Entity\Niveaux;
use App\Entity\Supplier;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\SupplierRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Symfony\Component\Clock\now;

class SupplierFixtures extends Fixture
{
	public const SUPPLIER_REFERENCE_TAG = 'supplier-';
	public const NB_SUPPLIER = 10;

	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create('fr_FR');
		// suppliers
		for ($i = 0; $i < self::NB_SUPPLIER; $i++) {
			$supplier = new Supplier();
			$supplier->setSupplierName($faker->userName());
			$supplier->setSupplierPhone($faker->phoneNumber());
			$supplier->setSupplierAddressNumber($faker->numberBetween(1, 150));
			$supplier->setSupplierAddressRoad($faker->lastName());
			$supplier->setSupplierAddressLabel($faker->lastName());
			$supplier->setSupplierAddressPostalCode($faker->numberBetween(10000, 98000));
			$supplier->setSupplierAddressCountry($faker->country());

			$manager->persist($supplier);
			$this->addReference(self::SUPPLIER_REFERENCE_TAG . $i, $supplier);
		}
		$manager->flush();
	}
}

