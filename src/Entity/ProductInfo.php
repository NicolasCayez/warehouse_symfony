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
    private ?string $product_info_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $product_info_content = null;

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
        return $this->product_info_name;
    }

    public function setProductInfoName(string $product_info_name): static
    {
        $this->product_info_name = $product_info_name;

        return $this;
    }

    public function getProductInfoContent(): ?string
    {
        return $this->product_info_content;
    }

    public function setProductInfoContent(string $product_info_content): static
    {
        $this->product_info_content = $product_info_content;

        return $this;
    }
}
