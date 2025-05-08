<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use App\Repository\ProductReceptionRepository;
use App\Repository\ProductRepository;
use App\Repository\StockModificationRepository;
use App\Repository\StockTransfertRepository;
use App\Service\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Family>
     */
    #[ORM\ManyToMany(targetEntity: Family::class, inversedBy: 'products')]
    private Collection $family;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    /**
     * @var Collection<int, ProductSize>
     */
    #[ORM\OneToMany(targetEntity: ProductSize::class, mappedBy: 'product_id', orphanRemoval: true)]
    private Collection $productSizes;

    /**
     * @var Collection<int, ProductColor>
     */
    #[ORM\OneToMany(targetEntity: ProductColor::class, mappedBy: 'product_id', orphanRemoval: true)]
    private Collection $productColors;

    /**
     * @var Collection<int, ProductInfo>
     */
    #[ORM\OneToMany(targetEntity: ProductInfo::class, mappedBy: 'product_id', orphanRemoval: true)]
    private Collection $productInfos;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'product_id')]
    private Collection $movements;

    /**
     * @var Collection<int, Warehouse>
     */
    #[ORM\ManyToMany(targetEntity: Warehouse::class, mappedBy: 'product_id')]
    private Collection $warehouses;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $productSerialNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $productName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $productRef = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $productRef2 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $productValue = null;

    public function __construct()
    {
        $this->family = new ArrayCollection();
        $this->productSizes = new ArrayCollection();
        $this->productColors = new ArrayCollection();
        $this->productInfos = new ArrayCollection();
        $this->movements = new ArrayCollection();
        $this->warehouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Family>
     */
    public function getFamily(): Collection
    {
        return $this->family;
    }

    public function addFamily(Family $family): static
    {
        if (!$this->family->contains($family)) {
            $this->family->add($family);
        }

        return $this;
    }

    public function removeFamily(Family $family): static
    {
        $this->family->removeElement($family);

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, ProductSize>
     */
    public function getProductSizes(): Collection
    {
        return $this->productSizes;
    }

    public function addProductSize(ProductSize $productSize): static
    {
        if (!$this->productSizes->contains($productSize)) {
            $this->productSizes->add($productSize);
            $productSize->setProduct($this);
        }

        return $this;
    }

    public function removeProductSize(ProductSize $productSize): static
    {
        if ($this->productSizes->removeElement($productSize)) {
            // set the owning side to null (unless already changed)
            if ($productSize->getProduct() === $this) {
                $productSize->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductColor>
     */
    public function getProductColors(): Collection
    {
        return $this->productColors;
    }

    public function addProductColor(ProductColor $productColor): static
    {
        if (!$this->productColors->contains($productColor)) {
            $this->productColors->add($productColor);
            $productColor->setProduct($this);
        }

        return $this;
    }

    public function removeProductColor(ProductColor $productColor): static
    {
        if ($this->productColors->removeElement($productColor)) {
            // set the owning side to null (unless already changed)
            if ($productColor->getProduct() === $this) {
                $productColor->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductInfo>
     */
    public function getProductInfos(): Collection
    {
        return $this->productInfos;
    }

    public function addProductInfo(ProductInfo $productInfo): static
    {
        if (!$this->productInfos->contains($productInfo)) {
            $this->productInfos->add($productInfo);
            $productInfo->setProduct($this);
        }

        return $this;
    }

    public function removeProductInfo(ProductInfo $productInfo): static
    {
        if ($this->productInfos->removeElement($productInfo)) {
            // set the owning side to null (unless already changed)
            if ($productInfo->getProduct() === $this) {
                $productInfo->setProduct(null);
            }
        }

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @return Collection<int, Movement>
     */
    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function addMovement(Movement $movement): static
    {
        if (!$this->movements->contains($movement)) {
            $this->movements->add($movement);
            $movement->setProduct($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getProduct() === $this) {
                $movement->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Warehouse>
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(Warehouse $warehouse): static
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses->add($warehouse);
            $warehouse->addProduct($this);
        }

        return $this;
    }

    public function removeWarehouse(Warehouse $warehouse): static
    {
        if ($this->warehouses->removeElement($warehouse)) {
            $warehouse->removeProduct($this);
        }

        return $this;
    }

    public function getProductSerialNumber(): ?string
    {
        return $this->productSerialNumber;
    }

    public function setProductSerialNumber(?string $productSerialNumber): static
    {
        $this->productSerialNumber = $productSerialNumber;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductRef(): ?string
    {
        return $this->productRef;
    }

    public function setProductRef(?string $productRef): static
    {
        $this->productRef = $productRef;

        return $this;
    }

    public function getProductRef2(): ?string
    {
        return $this->productRef2;
    }

    public function setProductRef2(?string $productRef2): static
    {
        $this->productRef2 = $productRef2;

        return $this;
    }

    public function getProductValue(): ?string
    {
        return $this->productValue;
    }

    public function setProductValue(?string $productValue): static
    {
        $this->productValue = $productValue;

        return $this;
    }

    public function getProductQuantity(Utils $utils,
                                    InventoryRepository $inventoryRepository,
                                    ProductReceptionRepository $productReceptionRepository,
                                    StockModificationRepository $stockModificationRepository,
                                    StockTransfertRepository $stockTransfertRepository,
                                    Warehouse $warehouse): ?string
    {
        return $utils->getProductQuantity($utils, $inventoryRepository, $productReceptionRepository, $stockModificationRepository, $stockTransfertRepository, $warehouse, $this);
    }
}
