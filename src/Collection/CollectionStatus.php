<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionStatus
{
    public function __construct(
        private bool $isOwned,
        private bool $isPreviouslyOwned,
        private bool $isForTrade,
        private bool $isWanted,
        private bool $isWantToPlay,
        private bool $isWantToBuy,
        private bool $isWishlisted,
        private ?int $wishlistPriority,
        private bool $isPreOrdered,
        private \DateTimeImmutable $lastModified,
    ) {}

    public function isOwned(): bool
    {
        return $this->isOwned;
    }

    public function isPreviouslyOwned(): bool
    {
        return $this->isPreviouslyOwned;
    }

    public function isForTrade(): bool
    {
        return $this->isForTrade;
    }

    public function isWanted(): bool
    {
        return $this->isWanted;
    }

    public function isWantToPlay(): bool
    {
        return $this->isWantToPlay;
    }

    public function isWantToBuy(): bool
    {
        return $this->isWantToBuy;
    }

    public function isWishlisted(): bool
    {
        return $this->isWishlisted;
    }

    public function getWishlistPriority(): ?int
    {
        return $this->wishlistPriority;
    }

    public function isPreOrdered(): bool
    {
        return $this->isPreOrdered;
    }

    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastModified;
    }
}
