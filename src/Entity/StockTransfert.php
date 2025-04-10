<?php

namespace App\Entity;

use App\Repository\StockTransfertRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockTransfertRepository::class)]
class StockTransfert extends Movement
{
    #[ORM\Column(type: Types::TEXT)]
    private ?string $stock_transfert_message = null;

    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'linkedStockTransfert', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?self $linkedTransfert = null;

    #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'linkedTransfert', cascade: ['persist', 'remove'])]
    private ?self $linkedStockTransfert = null;

    public function getStockTransfertMessage(): ?string
    {
        return $this->stock_transfert_message;
    }

    public function setStockTransfertMessage(string $stock_transfert_message): static
    {
        $this->stock_transfert_message = $stock_transfert_message;

        return $this;
    }

    public function getLinkedTransfert(): ?self
    {
        return $this->linkedTransfert;
    }

    public function setLinkedTransfert(self $linkedTransfert): static
    {
        $this->linkedTransfert = $linkedTransfert;

        return $this;
    }

    public function getLinkedStockTransfert(): ?self
    {
        return $this->linkedStockTransfert;
    }

    public function setLinkedStockTransfert(self $linkedStockTransfert): static
    {
        // set the owning side of the relation if necessary
        if ($linkedStockTransfert->getLinkedTransfert() !== $this) {
            $linkedStockTransfert->setLinkedTransfert($this);
        }

        $this->linkedStockTransfert = $linkedStockTransfert;

        return $this;
    }
}
