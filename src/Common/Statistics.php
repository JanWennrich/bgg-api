<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Statistics
{
    /**
     * @param Ratings[] $ratings
     */
    public function __construct(
        private string $page,
        private array $ratings,
    ) {}

    public function getPage(): string
    {
        return $this->page;
    }

    /**
     * @return Ratings[]
     */
    public function getRatings(): array
    {
        return $this->ratings;
    }
}
