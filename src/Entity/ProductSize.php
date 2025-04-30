<?php

namespace App\Entity;

use App\Repository\ProductSizeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductSizeRepository::class)]
class ProductSize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productSizes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(nullable: true)]
    private ?float $productSizeHeight = null;

    #[ORM\Column(nullable: true)]
    private ?float $productSizeWidth = null;

    #[ORM\Column(nullable: true)]
    private ?float $productSizeDepth = null;

    #[ORM\Column(nullable: true)]
    private ?float $productSizeWeight = null;

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

    public function getProductSizeHeight(): ?float
    {
        return $this->productSizeHeight;
    }

    public function setProductSizeHeight(?float $productSizeHeight): static
    {
        $this->productSizeHeight = $productSizeHeight;

        return $this;
    }

    public function getProductSizeWidth(): ?float
    {
        return $this->productSizeWidth;
    }

    public function setProductSizeWidth(?float $productSizeWidth): static
    {
        $this->productSizeWidth = $productSizeWidth;

        return $this;
    }

    public function getProductSizeDepth(): ?float
    {
        return $this->productSizeDepth;
    }

    public function setProductSizeDepth(?float $productSizeDepth): static
    {
        $this->productSizeDepth = $productSizeDepth;

        return $this;
    }

    public function getProductSizeWeight(): ?float
    {
        return $this->productSizeWeight;
    }

    public function setProductSizeWeight(?float $productSizeWeight): static
    {
        $this->productSizeWeight = $productSizeWeight;

        return $this;
    }
}
