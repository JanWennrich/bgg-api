<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class Listing
{
    public function __construct(
        private string $listDate,
        private ListingPrice $listingPrice,
        private string $condition,
        private string $notes,
        private ListingLink $listingLink,
    ) {}

    public function getListDate(): string
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
