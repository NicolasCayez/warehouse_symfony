<?php
  namespace App\Service;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Entity\Warehouse;
use App\Repository\InventoryRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\ProductRepository;
use App\Repository\StockModificationRepository;
use App\Repository\StockTransfertRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Length;


class Utils{
  /** Cette méthode permet de nettoyer les données entrées par l'utilisateur
  * @param string $value
  * @return null|string
  */
  public static function cleanInputStatic(string $value):?string {
    return htmlspecialchars(strip_tags(trim($value)));
  }

  public static function getLatestInventory(InventoryRepository $inventoryRepository, $warehouse):?Inventory {
    $inventoriesList = $inventoryRepository->findByWarehouse($warehouse);
    if ($inventoriesList > 0) {
      $inventory = new Inventory();
      $inventory->setInventoryDate(NEW DateTimeImmutable('1972-01-01', null));
      foreach ($inventoriesList as $inv) {
        if ($inv->getInventoryDate() > $inventory->getInventoryDate()) {
          $inventory = $inv;
        }
      }
      return $inventory;
    }
    // else, no inventory to return
    return null;
  }

  public static function getProductQuantity(Utils $utils,
                                            InventoryRepository $inventoryRepository,
                                            ProductReceptionRepository $productReceptionRepository,
                                            StockModificationRepository $stockModificationRepository,
                                            StockTransfertRepository $stockTransfertRepository,
                                            Warehouse $warehouse,
                                            Product $product
                                            ):?int {
    // Init
    $allMovementsSinceInventory = [];
    $latestInventory = $utils->getLatestInventory($inventoryRepository, $warehouse);
    $qty = 0;
    foreach ($latestInventory->getMovements() as $mvmt) {
      if ($mvmt->getProduct() == $product) {
        $qty = $mvmt->getNewQty();
      }
    }
    // get movements since inventory ang push results in the array
    foreach ($productReceptionRepository->findByWarehouse($warehouse) as $productReception) {
      if ($productReception->getProductReceptionDate() > $latestInventory->getInventoryDate()) {
        foreach ($productReception->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getNewQty();
          }
        }
      }
    }
    foreach ($stockModificationRepository->findByWarehouse($warehouse) as $stockModification) {
      if ($stockModification->getStockModificationDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockModification->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getNewQty();
          }
        }
      }
    }
    foreach ($stockTransfertRepository->findByWarehouse($warehouse) as $stockTransfert) {
      if ($stockTransfert->getStockTransfertDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockTransfert->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getNewQty();
          }
        }
      }
    }
    // return value
    return $qty;
  }
}

