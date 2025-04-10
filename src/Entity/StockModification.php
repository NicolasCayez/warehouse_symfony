<?php

namespace App\Entity;

use App\Repository\StockModificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockModificationRepository::class)]
class StockModification extends Movement
{
    #[ORM\Column(type: Types::TEXT)]
    private ?string $stock_modification_message = null;

    public function getStockModificationMessage(): ?string
    {
        return $this->stock_modification_message;
    }

    public function setStockModificationMessage(string $stock_modification_message): static
    {
        $this->stock_modification_message = $stock_modification_message;

        return $this;
    }
}
