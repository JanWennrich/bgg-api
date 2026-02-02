<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class ListingPrice
{
    public function __construct(
        private string $currency,
        private float $value,
    ) {}

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
