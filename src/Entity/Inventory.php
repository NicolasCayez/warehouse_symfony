<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $inventoryDate = null;

    #[ORM\Column]
    private ?bool $inventoryClosed = null;

    #[ORM\ManyToOne(inversedBy: 'inventories')]
    // #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'inventory')]
    private Collection $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInventoryDate(): ?\DateTimeImmutable
    {
        return $this->inventoryDate;
    }

    public function setInventoryDate(\DateTimeImmutable $inventoryDate): static
    {
        $this->inventoryDate = $inventoryDate;

        return $this;
    }

    public function isInventoryClosed(): ?bool
    {
        return $this->inventoryClosed;
    }

    public function setInventoryClosed(bool $inventoryClosed): static
    {
        $this->inventoryClosed = $inventoryClosed;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;

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
            $movement->setInventory($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getInventory() === $this) {
                $movement->setInventory(null);
            }
        }

        return $this;
    }
}
