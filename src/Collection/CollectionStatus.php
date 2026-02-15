<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionStatus
{
    public function __construct(
        private bool $own,
        private bool $prevOwned,
        private bool $forTrade,
        private bool $want,
        private bool $wantToPlay,
        private bool $wantToBuy,
        private bool $wishlist,
        private ?int $wishlistPriority,
        private bool $preordered,
        private \DateTimeImmutable $lastModified,
    ) {}

    public function isOwned(): bool
    {
        return $this->own;
    }

    public function isPreviouslyOwned(): bool
    {
        return $this->prevOwned;
    }

    public function isForTrade(): bool
    {
        return $this->forTrade;
    }

    public function isWanted(): bool
    {
        return $this->want;
    }

    public function isWantToPlay(): bool
    {
        return $this->wantToPlay;
    }

    public function isWantToBuy(): bool
    {
        return $this->wantToBuy;
    }

    public function isWishlisted(): bool
    {
        return $this->wishlist;
    }

    public function getWishlistPriority(): ?int
    {
        return $this->wishlistPriority;
    }

    public function isPreOrdered(): bool
    {
        return $this->preordered;
    }

    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastModified;
    }
}
