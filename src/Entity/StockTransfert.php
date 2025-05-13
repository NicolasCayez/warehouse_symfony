<?php

namespace App\Entity;

use App\Repository\StockTransfertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
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
    #[Assert\Length(min: 3,
                    max: 255,
                    minMessage: 'Write at least {{ limit }} charaters',
                    maxMessage: 'Write less than {{ limit }} characters')]
    private ?string $stockTransfertMessage = null;

    // #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'linkedStockTransfert', cascade: ['persist', 'remove'])]
    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'linkedStockTransfert', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    // #[ORM\JoinColumn]
    private ?self $linkedTransfert = null;
    //!!!!!!!!!!!!!!!
    #[ORM\Column(nullable: true)]
    private ?int $linkedStockTransfertId = null;
    //!!!!!!!!!!!!!!!

    // #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'linkedTransfert', cascade: ['persist', 'remove'])]
    #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'linkedTransfert')]
    private ?self $linkedStockTransfert = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $stockTransfertDate = null;

    #[ORM\ManyToOne(inversedBy: 'stockTransferts')]
    #[ORM\JoinColumn(nullable: false)]
    // #[ORM\JoinColumn]
    private ?Warehouse $warehouse = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'stockTransfert', cascade: ['persist', 'remove'])]
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
    //!!!!!!!!!!!!!!!!!!!!!!!
    public function removeLinkedTransfert(): static
    {
        $this->linkedTransfert = $this;
        
        return $this;
    }
    //!!!!!!!!!!!!!!!!!!!!!!!

    // public function getLinkedStockTransfert(StockTransfertRepository $stockTransfertRepository): ?self
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
    //!!!!!!!!!!!!!!!!!!!!!!!!!
    public function removeLinkedStockTransfert(): static
    {
        // set the owning side of the relation if necessary
        // $linkedStockTransfert = $this->getLinkedStockTransfert();
        // if ($linkedStockTransfert->getLinkedTransfert() !== $linkedStockTransfert) {
        //     $linkedStockTransfert->setLinkedTransfert($linkedStockTransfert);
        // }
        $this->linkedStockTransfert = $this;
        // $this->linkedStockTransfert = null;
        return $this;
    }
    //!!!!!!!!!!!!!!!!!!!!!!!!!
    // public function getLinkedStockTransfert(StockTransfertRepository $stockTransfertRepository): ?self
    // {
    //     $linkedStockTransfert = $stockTransfertRepository->findOneById($this->linkedStockTransfertId);
    //     return $linkedStockTransfert;
    // }
    public function getLinkedStockTransfertId(): int
    {
        return $this->linkedStockTransfertId;
    }

    public function setLinkedStockTransfertId(int $linkedStockTransfertId): static
    {
        $this->linkedStockTransfertId = $linkedStockTransfertId;

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
