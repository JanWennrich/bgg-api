<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionPrivateInfo
{
    public function __construct(
        private ?string $privateComment,
        private ?string $pricePaidCurrency,
        private ?float $pricePaid,
        private ?string $currentValueCurrency,
        private ?float $currentValue,
        private ?int $quantity,
        private ?string $acquisitionDate,
        private ?string $acquiredFrom,
        private ?string $inventoryLocation,
    ) {}

    public function getPrivateComment(): ?string
    {
        return $this->privateComment;
    }

    public function getPricePaidCurrency(): ?string
    {
        return $this->pricePaidCurrency;
    }

    public function getPricePaid(): ?float
    {
        return $this->pricePaid;
    }

    public function getCurrentValueCurrency(): ?string
    {
        return $this->currentValueCurrency;
    }

    public function getCurrentValue(): ?float
    {
        return $this->currentValue;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getAcquisitionDate(): ?string
    {
        return $this->acquisitionDate;
    }

    public function getAcquiredFrom(): ?string
    {
        return $this->acquiredFrom;
    }

    public function getInventoryLocation(): ?string
    {
        return $this->inventoryLocation;
    }
}
