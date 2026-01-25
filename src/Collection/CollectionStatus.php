<?php

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
        private bool $preordered,
        private string $lastModified,
    ) {}

    public function isOwn(): bool
    {
        return $this->own;
    }

    public function isPrevOwned(): bool
    {
        return $this->prevOwned;
    }

    public function isForTrade(): bool
    {
        return $this->forTrade;
    }

    public function isWant(): bool
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

    public function isWishlist(): bool
    {
        return $this->wishlist;
    }

    public function isPreordered(): bool
    {
        return $this->preordered;
    }

    public function getLastModified(): string
    {
        return $this->lastModified;
    }
}
