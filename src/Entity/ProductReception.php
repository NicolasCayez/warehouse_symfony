<?php

namespace App\Entity;

use App\Repository\ProductReceptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductReceptionRepository::class)]
class ProductReception
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $productReceptionInvoiceRef = null;

    #[ORM\Column(length: 50)]
    private ?string $productReceptionParcelRef = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $productReceptionDate = null;

    #[ORM\ManyToOne(inversedBy: 'productReceptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'productReception')]
    private Collection $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductReceptionInvoiceRef(): ?string
    {
        return $this->productReceptionInvoiceRef;
    }

    public function setProductReceptionInvoiceRef(string $productReceptionInvoiceRef): static
    {
        $this->productReceptionInvoiceRef = $productReceptionInvoiceRef;

        return $this;
    }

    public function getProductReceptionParcelRef(): ?string
    {
        return $this->productReceptionParcelRef;
    }

    public function setProductReceptionParcelRef(string $productReceptionParcelRef): static
    {
        $this->productReceptionParcelRef = $productReceptionParcelRef;

        return $this;
    }

    public function getProductReceptionDate(): ?\DateTimeImmutable
    {
        return $this->productReceptionDate;
    }

    public function setProductReceptionDate(\DateTimeImmutable $productReceptionDate): static
    {
        $this->productReceptionDate = $productReceptionDate;

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
            $movement->setProductReception($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getProductReception() === $this) {
                $movement->setProductReception(null);
            }
        }

        return $this;
    }
}
