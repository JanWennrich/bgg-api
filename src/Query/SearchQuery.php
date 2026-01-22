<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\SearchType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class SearchQuery
{
    /**
     * @param SearchType[] $onlyTypes Return all items that match the search query of the specified type(s).
     * @param bool $onlyExact Limit results to items that match the search query exactly.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public array $onlyTypes = [],
        public bool $onlyExact = false,
    ) {
        Assert::allIsInstanceOf($this->onlyTypes, SearchType::class);
    }
}
