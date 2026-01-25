<?php

namespace JanWennrich\BoardGameGeekApi\Search;

/**
 * @phpstan-type SearchResults Result[]
 */
class Search
{
    /**
     * @var SearchResults $results
     */
    private array $results = [];

    public function __construct(private readonly \SimpleXMLElement $root)
    {
        foreach ($this->root as $item) {
            $this->results[] = new Result($item);
        }
    }

    /**
     * @return SearchResults
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
