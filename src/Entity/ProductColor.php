<?php

namespace App\Entity;

use App\Repository\ProductColorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductColorRepository::class)]
class ProductColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productColors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(length: 50)]
    private ?string $productColorName = null;

    #[ORM\Column(length: 50)]
    private ?string $productColorLabel = null;

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

    public function getProductColorName(): ?string
    {
        return $this->productColorName;
    }

    public function setProductColorName(string $productColorName): static
    {
        $this->productColorName = $productColorName;

        return $this;
    }

    public function getProductColorLabel(): ?string
    {
        return $this->productColorLabel;
    }

    public function setProductColorLabel(string $productColorLabel): static
    {
        $this->productColorLabel = $productColorLabel;

        return $this;
    }
}
