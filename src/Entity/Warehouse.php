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
    private Collection $product_id;

    #[ORM\Column(length: 50)]
    private ?string $warehouse_name = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $warehouse_phone = null;

    #[ORM\Column(nullable: true)]
    private ?int $warehouse_address_number = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouse_address_road = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouse_address_label = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $warehouse_address_postal_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouse_address_city = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouse_address_state = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $warehouse_address_country = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'warehouse_id')]
    private Collection $users;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'warehouse_id')]
    private Collection $movements;

    public function __construct()
    {
        $this->product_id = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductId(): Collection
    {
        return $this->product_id;
    }

    public function addProductId(Product $productId): static
    {
        if (!$this->product_id->contains($productId)) {
            $this->product_id->add($productId);
        }

        return $this;
    }

    public function removeProductId(Product $productId): static
    {
        $this->product_id->removeElement($productId);

        return $this;
    }

    public function getWhName(): ?string
    {
        return $this->warehouse_name;
    }

    public function setWhName(string $warehouse_name): static
    {
        $this->warehouse_name = $warehouse_name;

        return $this;
    }

    public function getWhPhone(): ?string
    {
        return $this->warehouse_phone;
    }

    public function setWhPhone(?string $warehouse_phone): static
    {
        $this->warehouse_phone = $warehouse_phone;

        return $this;
    }

    public function getWhAddressNumber(): ?int
    {
        return $this->warehouse_address_number;
    }

    public function setWhAddressNumber(?int $warehouse_address_number): static
    {
        $this->warehouse_address_number = $warehouse_address_number;

        return $this;
    }

    public function getWhAddressRoad(): ?string
    {
        return $this->warehouse_address_road;
    }

    public function setWhAddressRoad(?string $warehouse_address_road): static
    {
        $this->warehouse_address_road = $warehouse_address_road;

        return $this;
    }

    public function getWhAddressLabel(): ?string
    {
        return $this->warehouse_address_label;
    }

    public function setWhAddressLabel(?string $warehouse_address_label): static
    {
        $this->warehouse_address_label = $warehouse_address_label;

        return $this;
    }

    public function getWhAddressPostalCode(): ?string
    {
        return $this->warehouse_address_postal_code;
    }

    public function setWhAddressPostalCode(?string $warehouse_address_postal_code): static
    {
        $this->warehouse_address_postal_code = $warehouse_address_postal_code;

        return $this;
    }

    public function getWhAddressCity(): ?string
    {
        return $this->warehouse_address_city;
    }

    public function setWhAddressCity(?string $warehouse_address_city): static
    {
        $this->warehouse_address_city = $warehouse_address_city;

        return $this;
    }

    public function getWhAddressState(): ?string
    {
        return $this->warehouse_address_state;
    }

    public function setWhAddressState(?string $warehouse_address_state): static
    {
        $this->warehouse_address_state = $warehouse_address_state;

        return $this;
    }

    public function getWhAddressCountry(): ?string
    {
        return $this->warehouse_address_country;
    }

    public function setWhAddressCountry(?string $warehouse_address_country): static
    {
        $this->warehouse_address_country = $warehouse_address_country;

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
            $movement->setWarehouse($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getWarehouse() === $this) {
                $movement->setWarehouse(null);
            }
        }

        return $this;
    }
}
