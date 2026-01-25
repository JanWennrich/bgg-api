<?php

namespace JanWennrich\BoardGameGeekApi;

use JanWennrich\BoardGameGeekApi\Collection\Item;

/**
 * @phpstan-type CollectionItems Item[]
 */
class Collection
{
    /**
     * @var CollectionItems
     */
    private array $items = [];

    public function __construct(private readonly \SimpleXMLElement $root)
    {
        foreach ($this->root as $item) {
            $this->items[] = new Collection\Item($item);
        }
    }

    /**
     * @return CollectionItems
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
