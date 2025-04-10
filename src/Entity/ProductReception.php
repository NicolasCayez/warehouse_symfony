<?php

namespace App\Entity;

use App\Repository\ProductReceptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductReceptionRepository::class)]
class ProductReception extends Movement
{
    #[ORM\Column(length: 50)]
    private ?string $invoice_ref = null;

    #[ORM\Column(length: 50)]
    private ?string $parcel_ref = null;

    public function getInvoiceRef(): ?string
    {
        return $this->invoice_ref;
    }

    public function setInvoiceRef(string $invoice_ref): static
    {
        $this->invoice_ref = $invoice_ref;

        return $this;
    }

    public function getParcelRef(): ?string
    {
        return $this->parcel_ref;
    }

    public function setParcelRef(string $parcel_ref): static
    {
        $this->parcel_ref = $parcel_ref;

        return $this;
    }
}
