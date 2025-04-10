<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory extends Movement
{
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $inventory_date = null;

    #[ORM\Column]
    private ?bool $inventory_closed = null;

    public function getInventoryDate(): ?\DateTimeImmutable
    {
        return $this->inventory_date;
    }

    public function setInventoryDate(\DateTimeImmutable $inventory_date): static
    {
        $this->inventory_date = $inventory_date;

        return $this;
    }

    public function isInventoryClosed(): ?bool
    {
        return $this->inventory_closed;
    }

    public function setInventoryClosed(bool $inventory_closed): static
    {
        $this->inventory_closed = $inventory_closed;

        return $this;
    }
}
