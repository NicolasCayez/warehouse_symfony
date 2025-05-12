<?php

namespace App\Entity;

use App\Repository\StockTransfertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockTransfertRepository::class)]
class StockTransfert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::TEXT)]
    #[ASSERT\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $stockTransfertMessage = null;

    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'linkedStockTransfert', cascade: ['persist', 'remove'])]
    // #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn]
    private ?self $linkedTransfert = null;

    #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'linkedTransfert', cascade: ['persist', 'remove'])]
    private ?self $linkedStockTransfert = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $stockTransfertDate = null;

    #[ORM\ManyToOne(inversedBy: 'stockTransferts')]
    // #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn]
    private ?Warehouse $warehouse = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'stockTransfert')]
    private Collection $movements;

    #[ORM\Column]
    private ?bool $transfertOrigin = null;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockTransfertMessage(): ?string
    {
        return $this->stockTransfertMessage;
    }

    public function setStockTransfertMessage(string $stockTransfertMessage): static
    {
        $this->stockTransfertMessage = $stockTransfertMessage;

        return $this;
    }

    public function getLinkedTransfert(): ?self
    {
        return $this->linkedTransfert;
    }

    public function setLinkedTransfert(self $linkedTransfert): static
    {
        $this->linkedTransfert = $linkedTransfert;

        return $this;
    }

    public function getLinkedStockTransfert(): ?self
    {
        return $this->linkedStockTransfert;
    }

    public function setLinkedStockTransfert(self $linkedStockTransfert): static
    {
        // set the owning side of the relation if necessary
        if ($linkedStockTransfert->getLinkedTransfert() !== $this) {
            $linkedStockTransfert->setLinkedTransfert($this);
        }

        $this->linkedStockTransfert = $linkedStockTransfert;

        return $this;
    }

    public function getStockTransfertDate(): ?\DateTimeImmutable
    {
        return $this->stockTransfertDate;
    }

    public function setStockTransfertDate(\DateTimeImmutable $stockTransfertDate): static
    {
        $this->stockTransfertDate = $stockTransfertDate;

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
            $movement->setStockTransfert($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getStockTransfert() === $this) {
                $movement->setStockTransfert(null);
            }
        }

        return $this;
    }

    public function isStockTransfertOrigin(): ?bool
    {
        return $this->transfertOrigin;
    }

    public function setStockTransfertOrigin(bool $transfertOrigin): static
    {
        $this->transfertOrigin = $transfertOrigin;

        return $this;
    }
}
