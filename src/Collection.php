<?php

namespace JanWennrich\BoardGameGeekApi;

use JanWennrich\BoardGameGeekApi\Collection\Item;

class Collection implements \IteratorAggregate, \Countable
{
    /** @var Collection\Item[] */
    private array $items = [];

    public function __construct(private \SimpleXMLElement $root)
    {
        foreach ($this->root as $item) {
            $this->items[] = new Collection\Item($item);
        }
    }

    /**
     * @return \ArrayIterator<Item>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return (int) $this->root['totalitems'];
    }
}
