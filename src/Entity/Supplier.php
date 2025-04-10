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
    private ?string $supplier_name = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $supplier_phone = null;

    #[ORM\Column(nullable: true)]
    private ?int $supplier_address_number = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplier_address_road = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplier_address_label = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $supplier_address_postal_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplier_address_city = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplier_address_state = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $supplier_address_country = null;

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
        return $this->supplier_name;
    }

    public function setSupplierName(string $supplier_name): static
    {
        $this->supplier_name = $supplier_name;

        return $this;
    }

    public function getSupplierPhone(): ?string
    {
        return $this->supplier_phone;
    }

    public function setSupplierPhone(?string $supplier_phone): static
    {
        $this->supplier_phone = $supplier_phone;

        return $this;
    }

    public function getSupplierAddressNumber(): ?int
    {
        return $this->supplier_address_number;
    }

    public function setSupplierAddressNumber(?int $supplier_address_number): static
    {
        $this->supplier_address_number = $supplier_address_number;

        return $this;
    }

    public function getSupplierAddressRoad(): ?string
    {
        return $this->supplier_address_road;
    }

    public function setSupplierAddressRoad(?string $supplier_address_road): static
    {
        $this->supplier_address_road = $supplier_address_road;

        return $this;
    }

    public function getSupplierAddressLabel(): ?string
    {
        return $this->supplier_address_label;
    }

    public function setSupplierAddressLabel(?string $supplier_address_label): static
    {
        $this->supplier_address_label = $supplier_address_label;

        return $this;
    }

    public function getSupplierAddressPostalCode(): ?string
    {
        return $this->supplier_address_postal_code;
    }

    public function setSupplierAddressPostalCode(?string $supplier_address_postal_code): static
    {
        $this->supplier_address_postal_code = $supplier_address_postal_code;

        return $this;
    }

    public function getSupplierAddressCity(): ?string
    {
        return $this->supplier_address_city;
    }

    public function setSupplierAddressCity(?string $supplier_address_city): static
    {
        $this->supplier_address_city = $supplier_address_city;

        return $this;
    }

    public function getSupplierAddressState(): ?string
    {
        return $this->supplier_address_state;
    }

    public function setSupplierAddressState(?string $supplier_address_state): static
    {
        $this->supplier_address_state = $supplier_address_state;

        return $this;
    }

    public function getSupplierAddressCountry(): ?string
    {
        return $this->supplier_address_country;
    }

    public function setSupplierAddressCountry(?string $supplier_address_country): static
    {
        $this->supplier_address_country = $supplier_address_country;

        return $this;
    }
}
