<?php

namespace App\Entity;

use App\Repository\ProductMovementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductMovementRepository::class)]
class ProductMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'productMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Movement $movement = null;

    #[ORM\Column]
    private ?int $last_qty = null;

    #[ORM\Column]
    private ?int $new_qty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getMovement(): ?Movement
    {
        return $this->movement;
    }

    public function setMovement(?Movement $movement): static
    {
        $this->movement = $movement;

        return $this;
    }

    public function getLastQty(): ?int
    {
        return $this->last_qty;
    }

    public function setLastQty(int $last_qty): static
    {
        $this->last_qty = $last_qty;

        return $this;
    }

    public function getNewQty(): ?int
    {
        return $this->new_qty;
    }

    public function setNewQty(int $new_qty): static
    {
        $this->new_qty = $new_qty;

        return $this;
    }
}
