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
    private ?string $product_color_name = null;

    #[ORM\Column(length: 50)]
    private ?string $product_color_label = null;

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
        return $this->product_color_name;
    }

    public function setProductColorName(string $product_color_name): static
    {
        $this->product_color_name = $product_color_name;

        return $this;
    }

    public function getProductColorLabel(): ?string
    {
        return $this->product_color_label;
    }

    public function setProductColorLabel(string $product_color_label): static
    {
        $this->product_color_label = $product_color_label;

        return $this;
    }
}
