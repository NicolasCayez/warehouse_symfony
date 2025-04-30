<?php

namespace App\Entity;

use App\Repository\ProductInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductInfoRepository::class)]
class ProductInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productInfos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(length: 50)]
    private ?string $productInfoName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $productInfoContent = null;

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

    public function getProductInfoName(): ?string
    {
        return $this->productInfoName;
    }

    public function setProductInfoName(string $productInfoName): static
    {
        $this->productInfoName = $productInfoName;

        return $this;
    }

    public function getProductInfoContent(): ?string
    {
        return $this->productInfoContent;
    }

    public function setProductInfoContent(string $productInfoContent): static
    {
        $this->productInfoContent = $productInfoContent;

        return $this;
    }
}
