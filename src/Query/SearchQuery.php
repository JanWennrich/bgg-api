<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\SearchType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class SearchQuery implements QueryInterface
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

    /**
     * @internal
     *
     * @return array{
     *     type?: non-empty-string,
     *     exact: int<0,1>
     * }
     */
    public function toQueryParameters(): array
    {
        $onlyTypesString = null;
        if ($this->onlyTypes !== []) {
            $onlyTypesString = implode(
                ',',
                array_map(
                    static fn(SearchType $searchType): string => $searchType->value,
                    $this->onlyTypes,
                ),
            );
        }

        return array_filter([
            'type' => $onlyTypesString,
            'exact' => (int) $this->onlyExact,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
