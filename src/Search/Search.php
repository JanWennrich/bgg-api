<?php

namespace JanWennrich\BoardGameGeekApi\Search;

final readonly class Search
{
    /**
     * @param SearchResult[] $results
     */
    public function __construct(
        private int $total,
        private array $results,
    ) {}

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return SearchResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
