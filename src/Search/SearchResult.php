<?php

namespace JanWennrich\BoardGameGeekApi\Search;

use JanWennrich\BoardGameGeekApi\SearchType;

final readonly class SearchResult
{
    public function __construct(
        private int $id,
        private ?SearchType $searchType,
        private SearchName $searchName,
        private ?int $yearPublished,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): ?SearchType
    {
        return $this->searchType;
    }

    public function getName(): SearchName
    {
        return $this->searchName;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }
}
