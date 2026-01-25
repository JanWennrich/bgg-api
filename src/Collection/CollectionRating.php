<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionRating
{
    /**
     * @param CollectionRank[] $ranks
     */
    public function __construct(
        private string $value,
        private int $usersRated,
        private float $average,
        private float $bayesAverage,
        private float $stddev,
        private float $median,
        private array $ranks,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUsersRated(): int
    {
        return $this->usersRated;
    }

    public function getAverage(): float
    {
        return $this->average;
    }

    public function getBayesAverage(): float
    {
        return $this->bayesAverage;
    }

    public function getStddev(): float
    {
        return $this->stddev;
    }

    public function getMedian(): float
    {
        return $this->median;
    }

    /**
     * @return CollectionRank[]
     */
    public function getRanks(): array
    {
        return $this->ranks;
    }
}
