<?php

namespace App\Entity;

use App\Repository\StockModificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockModificationRepository::class)]
class StockModification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::TEXT)]
    private ?string $stockModificationMessage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $stockModificationDate = null;

    #[ORM\ManyToOne(inversedBy: 'stockModifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'stockModification')]
    private Collection $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockModificationMessage(): ?string
    {
        return $this->stockModificationMessage;
    }

    public function setStockModificationMessage(string $stockModificationMessage): static
    {
        $this->stockModificationMessage = $stockModificationMessage;

        return $this;
    }

    public function getStockModificationDate(): ?\DateTimeImmutable
    {
        return $this->stockModificationDate;
    }

    public function setStockModificationDate(\DateTimeImmutable $stockModificationDate): static
    {
        $this->stockModificationDate = $stockModificationDate;

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
     * @return Collection<int, ProductMovement>
     */
    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function addMovement(Movement $movement): static
    {
        if (!$this->movements->contains($movement)) {
            $this->movements->add($movement);
            $movement->setStockModification($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getStockModification() === $this) {
                $movement->setStockModification(null);
            }
        }

        return $this;
    }
}
