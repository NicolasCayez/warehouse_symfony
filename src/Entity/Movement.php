<?php

namespace App\Entity;

use App\Repository\MovementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovementRepository::class)]
class Movement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'movements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $lastQty = null;

    #[ORM\Column]
    private ?int $newQty = null;

    #[ORM\ManyToOne(inversedBy: 'movements')]
    private ?ProductReception $productReception = null;

    #[ORM\ManyToOne(inversedBy: 'Movements')]
    private ?StockModification $stockModification = null;

    #[ORM\ManyToOne(inversedBy: 'movements')]
    private ?StockTransfert $stockTransfert = null;

    #[ORM\ManyToOne(inversedBy: 'movements')]
    private ?Inventory $inventory = null;

    #[ORM\Column(length: 20)]
    private ?string $movementType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getLastQty(): ?int
    {
        return $this->lastQty;
    }

    public function setLastQty(int $lastQty): static
    {
        $this->lastQty = $lastQty;

        return $this;
    }

    public function getNewQty(): ?int
    {
        return $this->newQty;
    }

    public function setNewQty(int $newQty): static
    {
        $this->newQty = $newQty;

        return $this;
    }

    public function getProductReception(): ?ProductReception
    {
        return $this->productReception;
    }

    public function setProductReception(?ProductReception $productReception): static
    {
        $this->productReception = $productReception;

        return $this;
    }

    public function getStockModification(): ?StockModification
    {
        return $this->stockModification;
    }

    public function setStockModification(?StockModification $stockModification): static
    {
        $this->stockModification = $stockModification;

        return $this;
    }

    public function getStockTransfert(): ?StockTransfert
    {
        return $this->stockTransfert;
    }

    public function setStockTransfert(?StockTransfert $stockTransfert): static
    {
        $this->stockTransfert = $stockTransfert;

        return $this;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): static
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getMovementType(): ?string
    {
        return $this->movementType;
    }

    public function setMovementType(string $movementType): static
    {
        $this->movementType = $movementType;

        return $this;
    }
}
