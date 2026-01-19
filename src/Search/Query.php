<?php

namespace JanWennrich\BoardGameGeekApi\Search;

use JanWennrich\BoardGameGeekApi\Exception;

/**
 * @implements \ArrayAccess<integer, Result>
 * @implements \IteratorAggregate<integer, Result>
 */
class Query implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /** @var Result[] */
    private array $results = [];

    public function __construct(private readonly \SimpleXMLElement $root)
    {
        foreach ($this->root as $item) {
            $this->results[] = new Result($item);
        }
    }

    /**
     * @return \ArrayIterator<int, Result>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    public function count(): int
    {
        return count($this->results);
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
