<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Cours;
use App\Entity\Family;
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

class FamilyBrandFixtures extends Fixture
{
	public const FAMILY_REFERENCE_TAG = 'family-';
	public const BRAND_REFERENCE_TAG = 'brand-';

	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create('fr_FR');
		// families
		$i = 0;
		// 1
		$family = new Family();
		$family->setFamilyName("Computer");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 2
		$family = new Family();
		$family->setFamilyName("Laptop");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 3
		$family = new Family();
		$family->setFamilyName("CPU");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 4
		$family = new Family();
		$family->setFamilyName("GPU");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 5
		$family = new Family();
		$family->setFamilyName("PSU");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 6
		$family = new Family();
		$family->setFamilyName("SSD");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 7
		$family = new Family();
		$family->setFamilyName("Memory");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 8
		$family = new Family();
		$family->setFamilyName("Keyboard");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);
		// 9
		$family = new Family();
		$family->setFamilyName("Mouse");
		$manager->persist($family);
		$i ++;
		$this->addReference(self::FAMILY_REFERENCE_TAG . $i, $family);

		// brands
		$i = 0;
		// 1
		$brand = new Brand();
		$brand->setBrandName("Asus");
		$manager->persist($brand);
		$i ++;
		$this->addReference(self::BRAND_REFERENCE_TAG . $i, $brand);
		// 2
		$brand = new Brand();
		$brand->setBrandName("Msi");
		$manager->persist($brand);
		$i ++;
		$this->addReference(self::BRAND_REFERENCE_TAG . $i, $brand);
		// 3
		$brand = new Brand();
		$brand->setBrandName("Corsair");
		$manager->persist($brand);
		$i ++;
		$this->addReference(self::BRAND_REFERENCE_TAG . $i, $brand);
		// 4
		$brand = new Brand();
		$brand->setBrandName("Dell");
		$manager->persist($brand);
		$i ++;
		$this->addReference(self::BRAND_REFERENCE_TAG . $i, $brand);
		// 5
		$brand = new Brand();
		$brand->setBrandName("HP");
		$manager->persist($brand);
		$i ++;
		$this->addReference(self::BRAND_REFERENCE_TAG . $i, $brand);

		// Flush
		$manager->flush();
	}
}

