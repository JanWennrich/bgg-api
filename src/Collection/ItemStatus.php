<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

class ItemStatus
{
    private bool $own;

    private bool $prevOwned;

    private bool $forTrade;

    private bool $want;

    private bool $wantToPlay;

    private bool $wantToBuy;

    private bool $wishlist;

    private ?int $wishlistPriority;

    private bool $preordered;

    private ?\DateTimeImmutable $lastModified;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->own = $this->toBool($xml['own'] ?? null);
        $this->prevOwned = $this->toBool($xml['prevowned'] ?? null);
        $this->forTrade = $this->toBool($xml['fortrade'] ?? null);
        $this->want = $this->toBool($xml['want'] ?? null);
        $this->wantToPlay = $this->toBool($xml['wanttoplay'] ?? null);
        $this->wantToBuy = $this->toBool($xml['wanttobuy'] ?? null);
        $this->wishlist = $this->toBool($xml['wishlist'] ?? null);
        $this->wishlistPriority = $this->toNullableInt($xml['wishlistpriority'] ?? null);
        $this->preordered = $this->toBool($xml['preordered'] ?? null);
        $this->lastModified = $this->toDate((string) ($xml['lastmodified'] ?? ''));
    }

    private function toBool(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $v = strtolower(trim($value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }

    private function toNullableInt(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $s = trim($value);
        if ($s === '' || !is_numeric($s)) {
            return null;
        }

        return (int) $s;
    }

    private function toDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }

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

    public function getWishlistPriority(): ?int
    {
        return $this->wishlistPriority;
    }

    public function isPreordered(): bool
    {
        return $this->preordered;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }
}
