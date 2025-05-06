<?php

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'warehouses')]
    private Collection $products;

    #[ORM\Column(length: 50)]
    private ?string $warehouseName = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $warehousePhone = null;

    #[ORM\Column(nullable: true)]
    private ?int $warehouseAddressNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouseAddressRoad = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouseAddressLabel = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $warehouseAddressPostalCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouseAddressCity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouseAddressState = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouseAddressCountry = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'warehouseId')]
    private Collection $users;

    /**
     * @var Collection<int, Inventory>
     */
    #[ORM\OneToMany(targetEntity: Inventory::class, mappedBy: 'warehouse', cascade: ['persist'])]
    private Collection $inventories;

    /**
     * @var Collection<int, ProductReception>
     */
    #[ORM\OneToMany(targetEntity: ProductReception::class, mappedBy: 'warehouse')]
    private Collection $productReceptions;

    /**
     * @var Collection<int, StockTransfert>
     */
    #[ORM\OneToMany(targetEntity: StockTransfert::class, mappedBy: 'warehouse')]
    private Collection $stockTransferts;

    /**
     * @var Collection<int, StockModification>
     */
    #[ORM\OneToMany(targetEntity: StockModification::class, mappedBy: 'warehouse')]
    private Collection $stockModifications;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->inventories = new ArrayCollection();
        $this->productReceptions = new ArrayCollection();
        $this->stockTransferts = new ArrayCollection();
        $this->stockModifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getWarehouseName(): ?string
    {
        return $this->warehouseName;
    }

    public function setWarehouseName(string $warehouseName): static
    {
        $this->warehouseName = $warehouseName;

        return $this;
    }

    public function getWarehousePhone(): ?string
    {
        return $this->warehousePhone;
    }

    public function setWarehousePhone(?string $warehousePhone): static
    {
        $this->warehousePhone = $warehousePhone;

        return $this;
    }

    public function getWarehouseAddressNumber(): ?int
    {
        return $this->warehouseAddressNumber;
    }

    public function setWarehouseAddressNumber(?int $warehouseAddressNumber): static
    {
        $this->warehouseAddressNumber = $warehouseAddressNumber;

        return $this;
    }

    public function getWarehouseAddressRoad(): ?string
    {
        return $this->warehouseAddressRoad;
    }

    public function setWarehouseAddressRoad(?string $warehouseAddressRoad): static
    {
        $this->warehouseAddressRoad = $warehouseAddressRoad;

        return $this;
    }

    public function getWarehouseAddressLabel(): ?string
    {
        return $this->warehouseAddressLabel;
    }

    public function setWarehouseAddressLabel(?string $warehouseAddressLabel): static
    {
        $this->warehouseAddressLabel = $warehouseAddressLabel;

        return $this;
    }

    public function getWarehouseAddressPostalCode(): ?string
    {
        return $this->warehouseAddressPostalCode;
    }

    public function setWarehouseAddressPostalCode(?string $warehouseAddressPostalCode): static
    {
        $this->warehouseAddressPostalCode = $warehouseAddressPostalCode;

        return $this;
    }

    public function getWarehouseAddressCity(): ?string
    {
        return $this->warehouseAddressCity;
    }

    public function setWarehouseAddressCity(?string $warehouseAddressCity): static
    {
        $this->warehouseAddressCity = $warehouseAddressCity;

        return $this;
    }

    public function getWarehouseAddressState(): ?string
    {
        return $this->warehouseAddressState;
    }

    public function setWarehouseAddressState(?string $warehouseAddressState): static
    {
        $this->warehouseAddressState = $warehouseAddressState;

        return $this;
    }

    public function getWarehouseAddressCountry(): ?string
    {
        return $this->warehouseAddressCountry;
    }

    public function setWarehouseAddressCountry(?string $warehouseAddressCountry): static
    {
        $this->warehouseAddressCountry = $warehouseAddressCountry;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addWarehouse($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeWarehouse($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Inventory>
     */
    public function getInventories(): Collection
    {
        return $this->inventories;
    }

    public function addInventory(Inventory $inventory): static
    {
        if (!$this->inventories->contains($inventory)) {
            $this->inventories->add($inventory);
            $inventory->setWarehouse($this);
        }

        return $this;
    }

    public function removeInventory(Inventory $inventory): static
    {
        if ($this->inventories->removeElement($inventory)) {
            // set the owning side to null (unless already changed)
            if ($inventory->getWarehouse() === $this) {
                $inventory->setWarehouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductReception>
     */
    public function getProductReceptions(): Collection
    {
        return $this->productReceptions;
    }

    public function addProductReception(ProductReception $productReception): static
    {
        if (!$this->productReceptions->contains($productReception)) {
            $this->productReceptions->add($productReception);
            $productReception->setWarehouse($this);
        }

        return $this;
    }

    public function removeProductReception(ProductReception $productReception): static
    {
        if ($this->productReceptions->removeElement($productReception)) {
            // set the owning side to null (unless already changed)
            if ($productReception->getWarehouse() === $this) {
                $productReception->setWarehouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockTransfert>
     */
    public function getStockTransferts(): Collection
    {
        return $this->stockTransferts;
    }

    public function addStockTransfert(StockTransfert $stockTransfert): static
    {
        if (!$this->stockTransferts->contains($stockTransfert)) {
            $this->stockTransferts->add($stockTransfert);
            $stockTransfert->setWarehouse($this);
        }

        return $this;
    }

    public function removeStockTransfert(StockTransfert $stockTransfert): static
    {
        if ($this->stockTransferts->removeElement($stockTransfert)) {
            // set the owning side to null (unless already changed)
            if ($stockTransfert->getWarehouse() === $this) {
                $stockTransfert->setWarehouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockModification>
     */
    public function getStockModifications(): Collection
    {
        return $this->stockModifications;
    }

    public function addStockModification(StockModification $stockModification): static
    {
        if (!$this->stockModifications->contains($stockModification)) {
            $this->stockModifications->add($stockModification);
            $stockModification->setWarehouse($this);
        }

        return $this;
    }

    public function removeStockModification(StockModification $stockModification): static
    {
        if ($this->stockModifications->removeElement($stockModification)) {
            // set the owning side to null (unless already changed)
            if ($stockModification->getWarehouse() === $this) {
                $stockModification->setWarehouse(null);
            }
        }

        return $this;
    }

}
