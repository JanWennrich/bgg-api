<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class PollResults
{
    /**
     * @param PollResult[] $results
     */
    public function __construct(
        private ?string $numPlayers,
        private array $results,
    ) {}

    public function getNumPlayers(): ?string
    {
        return $this->numPlayers;
    }

    /**
     * @return PollResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
