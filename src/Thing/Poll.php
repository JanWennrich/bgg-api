<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class Poll
{
    /**
     * @param PollResults[] $results
     */
    public function __construct(
        private string $name,
        private string $title,
        private int $totalVotes,
        private array $results,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTotalVotes(): int
    {
        return $this->totalVotes;
    }

    /**
     * @return PollResults[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
