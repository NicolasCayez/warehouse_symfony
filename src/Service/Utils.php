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
use DateTime;
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

  public static function getLatestInventory($warehouse):?Inventory {
    $inventoriesList = $warehouse->getInventories();
    if ($inventoriesList) {
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

  public static function getLatestInventoryByDateTime($warehouse, $dateTime):?Inventory {
    $inventoriesList = $warehouse->getInventories();
    if ($inventoriesList) {
      $inventory = new Inventory();
      $inventory->setInventoryDate(NEW DateTimeImmutable('1972-01-01', null));
      foreach ($inventoriesList as $inv) {
        if ($inv->getInventoryDate() > $dateTime) {
          $inventoriesList->remove($inv);
        } elseif ($inv->getInventoryDate() <= $dateTime  && $inv->getInventoryDate() > $inventory->getInventoryDate()) {
          $inventory = $inv;
        }
      }
      return $inventory;
    }
    // else, no inventory to return
    return null;
  }

  public static function getProductQuantity(
      Utils $utils,
      Warehouse $warehouse,
      Product $product
    ):?int {
    // Init
    $latestInventory = $utils->getLatestInventory($warehouse);
    $qty = 0;
    foreach ($latestInventory->getMovements() as $mvmt) {
      if ($mvmt->getProduct() == $product) {
        $qty = $mvmt->getMovementQty();
      }
    }
    // get movements since inventory ang push results in the array
    foreach ($warehouse->getProductReceptions() as $productReception) {
      if ($productReception->getProductReceptionDate() > $latestInventory->getInventoryDate()) {
        foreach ($productReception->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getMovementQty();
          }
        }
      }
    }
    foreach ($warehouse->getStockModifications() as $stockModification) {
      if ($stockModification->getStockModificationDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockModification->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getNewQty();
          }
        }
      }
    }
    foreach ($warehouse->getStockTransferts() as $stockTransfert) {
      if ($stockTransfert->getStockTransfertDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockTransfert->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getMovementQty();
          }
        }
      }
    }
    return $qty;
  }

  public static function getProductQuantityByDateTime(
      Utils $utils,
      Warehouse $warehouse,
      DateTimeImmutable $dateTime,
      Product $product
    ):?int {
    // Init
    $latestInventory = $utils->getLatestInventoryByDateTime($warehouse, $dateTime);
    $qty = 0;
    foreach ($latestInventory->getMovements() as $mvmt) {
      if ($mvmt->getProduct() == $product) {
        $qty = $mvmt->getMovementQty();
      }
    }
    // get movements since inventory ang push results in the array
    foreach ($warehouse->getProductReceptions() as $productReception) {
      if ($productReception->getProductReceptionDate() > $latestInventory->getInventoryDate()) {
        foreach ($productReception->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getMovementQty();
          }
        }
      }
    }
    foreach ($warehouse->getStockModifications() as $stockModification) {
      if ($stockModification->getStockModificationDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockModification->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getNewQty();
          }
        }
      }
    }
    foreach ($warehouse->getStockTransferts() as $stockTransfert) {
      if ($stockTransfert->getStockTransfertDate() > $latestInventory->getInventoryDate()) {
        foreach ($stockTransfert->getMovements() as $mvmt) {
          if ($mvmt->getProduct() == $product) {
            $qty = $qty + $mvmt->getMovementQty();
          }
        }
      }
    }
    return $qty;
  }

}

