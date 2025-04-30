<?php

namespace App\DataFixtures;

use App\Entity\Cours;
use App\Entity\Langages;
use App\Entity\Niveaux;
use App\Entity\Product;
use App\Entity\ProductInfo;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Repository\BrandRepository;
use App\Repository\FamilyRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductInfoRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\SupplierRepository;
use App\Repository\UserRepository;
use App\Repository\WarehouseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Symfony\Component\Clock\now;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
	public const PRODUCT_REFERENCE_TAG = 'product-';
	public const NB_PRODUCT = 200;
	private WarehouseRepository $warehouseRepository;
	private ProductRepository $productRepository;
	private FamilyRepository $familyRepository;
	private BrandRepository $brandRepository;
	private SupplierRepository $supplierRepository;

	public function __construct(WarehouseRepository $warehouseRepository,
															ProductRepository $productRepository,
															FamilyRepository $familyRepository,
															BrandRepository $brandRepository,
															SupplierRepository $supplierRepository,
															)
	{
		$this->warehouseRepository = $warehouseRepository;
		$this->productRepository = $productRepository;
		$this->familyRepository = $familyRepository;
		$this->brandRepository = $brandRepository;
		$this->supplierRepository = $supplierRepository;
	}

	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create('fr_FR');
		// link product to ALL_DATA warahouse
		$all_data_warehouse = $this->warehouseRepository->findOneByWarehouse_name('ALL_DATA');
		// Families
		$families = $this->familyRepository->findAll();
		// Brands
		$brands = $this->brandRepository->findAll();
		// Suppliers
		$suppliers = $this->supplierRepository->findAll();
		// products
		for ($i = 0; $i < self::NB_PRODUCT; $i++) {
			// Random Family
			$family = $families[array_rand($families, 1)];
			// Random Brand
			$brand = $brands[array_rand($brands, 1)];
			// Random Supplier
			$supplier = $suppliers[array_rand($suppliers, 1)];
			// Product creation
			$product = new Product();
			$product->setProductSerialNumber($faker->numberBetween(10000, 1500000));
			$product->setProductName($faker->userName());
			$product->setProductRef($faker->userName());
			$product->setProductRef2($faker->userName());
			$product->setProductValue($faker->randomFloat(2, 5, 3000));
			$product->addWarehouse($all_data_warehouse);
			$product->addFamily($family);
			$product->setBrand($brand);
			$product->setSupplier($supplier);
			$manager->persist($product);
			$this->addReference(self::PRODUCT_REFERENCE_TAG . $i, $product);
			$manager->flush();
			$lastId = $product->getId();
			$lastProduct = $this->productRepository->findOneById($lastId);
			// link product to ALL_DATA warahouse
			$all_data_warehouse->addProduct($lastProduct);
			// Family
			$family->addProduct($lastProduct);
			// Brand
			$brand->addProduct($lastProduct);
			// Supplier
			$supplier->addProduct($lastProduct);
		}
		$manager->flush();
	}

		public function getDependencies(): array
	{
			return [
					WarehouseFixtures::class,
					SupplierFixtures::class,
			];
	}
}

