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
  /** Cleans the user inputs
  * @param string $value
  * @return null|string
  */
  public static function cleanInputStatic(string $value):?string {
    return htmlspecialchars(strip_tags(trim($value)));
  }

  /** Gets the latest inventory for one warehouse
  * @param Warehouse $warehouse
  * @return null|Inventory
  */
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

  /** Gets the latest inventory for one warehouse, by date
  * @param Warehouse $warehouse
  * @param DateTimeImmutable $dateTime
  * @return null|Inventory
  */
  public static function getLatestInventoryByDateTime($warehouse, $dateTime):?Inventory {
    $inventoriesList = $warehouse->getInventories();
    if ($inventoriesList) {
      $inventory = new Inventory();
      $inventory->setInventoryDate(NEW DateTimeImmutable('1972-01-01', null));
      foreach ($inventoriesList as $inv) {
        if ($inv->getInventoryDate() > $dateTime) {
          $inventoriesList->removeElement($inv);
        } elseif ($inv->getInventoryDate() <= $dateTime  && $inv->getInventoryDate() > $inventory->getInventoryDate()) {
          $inventory = $inv;
        }
      }
      return $inventory;
    }
    // else, no inventory to return
    return null;
  }

  /** Gets the product quantity for one warehouse
  * @param Utils $utils
  * @param Warehouse $warehouse
  * @param Product $product
  * @return 0|Integer
  */
  public static function getProductQuantity(
      Utils $utils,
      StockTransfertRepository $stockTransfertRepository,
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
        if ($stockTransfert->isStockTransfertOrigin()) {
          foreach ($stockTransfert->getMovements() as $mvmt) {
            if ($mvmt->getProduct() == $product) {
              $qty = $qty - $mvmt->getMovementQty();
            }
          }
        } else {
          foreach ($stockTransfert->getLinkedStockTransfert($stockTransfertRepository)->getMovements() as $mvmt) {
            if ($mvmt->getProduct() == $product) {
              $qty = $qty + $mvmt->getMovementQty();
            }
          }
        }
      }
    }
    return $qty;
  }

    /** Gets the product quantity for one warehouse, by date
  * @param Utils $utils
  * @param Warehouse $warehouse
  * @param DateTimeImmutable $dateTime
  * @param Product $product
  * @return 0|Integer
  */
  public static function getProductQuantityByDateTime(
      Utils $utils,
      StockTransfertRepository $stockTransfertRepository,
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
        if ($stockTransfert->isStockTransfertOrigin()) {
          foreach ($stockTransfert->getMovements() as $mvmt) {
            if ($mvmt->getProduct() == $product) {
              $qty = $qty - $mvmt->getMovementQty();
            }
          }
        } else {
          foreach ($stockTransfert->getLinkedStockTransfert($stockTransfertRepository)->getMovements() as $mvmt) {
            if ($mvmt->getProduct() == $product) {
              $qty = $qty + $mvmt->getMovementQty();
            }
          }
        }
      }
    }
    return $qty;
  }

}

