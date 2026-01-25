<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class PollResult
{
    public function __construct(
        private string $value,
        private int $numVotes,
        private ?int $level,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getNumVotes(): int
    {
        return $this->numVotes;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }
}
