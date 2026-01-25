<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class Collection
{
    /**
     * @param CollectionItem[] $items
     */
    public function __construct(
        private int $totalItems,
        private string $pubDate,
        private array $items,
    ) {}

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getPubDate(): string
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
