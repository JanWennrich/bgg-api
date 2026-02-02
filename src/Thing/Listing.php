<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class Listing
{
    public function __construct(
        private ?\DateTimeImmutable $listDate,
        private ListingPrice $listingPrice,
        private string $condition,
        private string $notes,
        private ListingLink $listingLink,
    ) {}

    public function getListDate(): ?\DateTimeImmutable
    {
        return $this->listDate;
    }

    public function getPrice(): ListingPrice
    {
        return $this->listingPrice;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getLink(): ListingLink
    {
        return $this->listingLink;
    }
}
