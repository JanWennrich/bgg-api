<?php

namespace JanWennrich\BoardGameGeekApi\Search;

use JanWennrich\BoardGameGeekApi\Exception;

class Query implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /** @var Result[] */
    private $results = [];

    public function __construct(private \SimpleXMLElement $root)
    {
        foreach ($this->root as $item) {
            $this->results[] = new Result($item);
        }
    }

    /**
     * @return \ArrayIterator<Result>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    public function count(): int
    {
        return (int) $this->root['total'];
    }

    /**
     * @param  int $offset
     * @param  mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new Exception('Search\\Query is read-only.');
    }

    /**
     * @param  int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->results[ $offset ]);
    }

    /**
     * @param  int $offset
     * @throws Exception
     */
    public function offsetUnset($offset): void
    {
        throw new Exception('Search\\Query is read-only.');
    }

    public function offsetGet($offset): ?Result
    {
        return $this->results[$offset] ?? null;
    }

    /**
     * @return Result[]
     */
    public function toArray(): array
    {
        return $this->results;
    }
}
