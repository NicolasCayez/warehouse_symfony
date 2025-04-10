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
    private ?float $product_size_height = null;

    #[ORM\Column(nullable: true)]
    private ?float $product_size_width = null;

    #[ORM\Column(nullable: true)]
    private ?float $product_size_depth = null;

    #[ORM\Column(nullable: true)]
    private ?float $product_size_weight = null;

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
        return $this->product_size_height;
    }

    public function setProductSizeHeight(?float $product_size_height): static
    {
        $this->product_size_height = $product_size_height;

        return $this;
    }

    public function getProductSizeWidth(): ?float
    {
        return $this->product_size_width;
    }

    public function setProductSizeWidth(?float $product_size_width): static
    {
        $this->product_size_width = $product_size_width;

        return $this;
    }

    public function getProductSizeDepth(): ?float
    {
        return $this->product_size_depth;
    }

    public function setProductSizeDepth(?float $product_size_depth): static
    {
        $this->product_size_depth = $product_size_depth;

        return $this;
    }

    public function getProductSizeWeight(): ?float
    {
        return $this->product_size_weight;
    }

    public function setProductSizeWeight(?float $product_size_weight): static
    {
        $this->product_size_weight = $product_size_weight;

        return $this;
    }
}
