<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'supplier')]
    private Collection $products;

    #[ORM\Column(length: 50)]
    private ?string $supplierName = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $supplierPhone = null;

    #[ORM\Column(nullable: true)]
    private ?int $supplierAddressNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplierAddressRoad = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplierAddressLabel = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $supplierAddressPostalCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplierAddressCity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplierAddressState = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplierAddressCountry = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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
            $product->setSupplier($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSupplier() === $this) {
                $product->setSupplier(null);
            }
        }

        return $this;
    }

    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    public function setSupplierName(string $supplierName): static
    {
        $this->supplierName = $supplierName;

        return $this;
    }

    public function getSupplierPhone(): ?string
    {
        return $this->supplierPhone;
    }

    public function setSupplierPhone(?string $supplierPhone): static
    {
        $this->supplierPhone = $supplierPhone;

        return $this;
    }

    public function getSupplierAddressNumber(): ?int
    {
        return $this->supplierAddressNumber;
    }

    public function setSupplierAddressNumber(?int $supplierAddressNumber): static
    {
        $this->supplierAddressNumber = $supplierAddressNumber;

        return $this;
    }

    public function getSupplierAddressRoad(): ?string
    {
        return $this->supplierAddressRoad;
    }

    public function setSupplierAddressRoad(?string $supplierAddressRoad): static
    {
        $this->supplierAddressRoad = $supplierAddressRoad;

        return $this;
    }

    public function getSupplierAddressLabel(): ?string
    {
        return $this->supplierAddressLabel;
    }

    public function setSupplierAddressLabel(?string $supplierAddressLabel): static
    {
        $this->supplierAddressLabel = $supplierAddressLabel;

        return $this;
    }

    public function getSupplierAddressPostalCode(): ?string
    {
        return $this->supplierAddressPostalCode;
    }

    public function setSupplierAddressPostalCode(?string $supplierAddressPostalCode): static
    {
        $this->supplierAddressPostalCode = $supplierAddressPostalCode;

        return $this;
    }

    public function getSupplierAddressCity(): ?string
    {
        return $this->supplierAddressCity;
    }

    public function setSupplierAddressCity(?string $supplierAddressCity): static
    {
        $this->supplierAddressCity = $supplierAddressCity;

        return $this;
    }

    public function getSupplierAddressState(): ?string
    {
        return $this->supplierAddressState;
    }

    public function setSupplierAddressState(?string $supplierAddressState): static
    {
        $this->supplierAddressState = $supplierAddressState;

        return $this;
    }

    public function getSupplierAddressCountry(): ?string
    {
        return $this->supplierAddressCountry;
    }

    public function setSupplierAddressCountry(?string $supplierAddressCountry): static
    {
        $this->supplierAddressCountry = $supplierAddressCountry;

        return $this;
    }
}
