<?php

namespace JanWennrich\BoardGameGeekApi\Family;

final readonly class FamilyItems
{
    /**
     * @param FamilyItem[] $items
     */
    public function __construct(
        private array $items,
    ) {}

    /**
     * @return FamilyItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
