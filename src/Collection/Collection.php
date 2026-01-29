<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class Collection
{
    /**
     * @param CollectionItem[] $items
     */
    public function __construct(
        private int $totalItems,
        private ?\DateTimeImmutable $pubDate,
        private array $items,
    ) {}

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getPubDate(): ?\DateTimeImmutable
    {
        return $this->pubDate;
    }

    /**
     * @return CollectionItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
