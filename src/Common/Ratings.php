<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Ratings
{
    /**
     * @param Rank[]|null $ranks
     */
    public function __construct(
        private ?\DateTimeImmutable $date,
        private int $usersRated,
        private float $average,
        private float $bayesAverage,
        private ?float $stddev,
        private ?float $median,
        private int $owned,
        private int $trading,
        private int $wanting,
        private int $wishing,
        private int $numComments,
        private int $numWeights,
        private float $averageWeight,
        private ?array $ranks,
    ) {}

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
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

    public function getStddev(): ?float
    {
        return $this->stddev;
    }

    public function getMedian(): ?float
    {
        return $this->median;
    }

    public function getOwned(): int
    {
        return $this->owned;
    }

    public function getTrading(): int
    {
        return $this->trading;
    }

    public function getWanting(): int
    {
        return $this->wanting;
    }

    public function getWishing(): int
    {
        return $this->wishing;
    }

    public function getNumComments(): int
    {
        return $this->numComments;
    }

    public function getNumWeights(): int
    {
        return $this->numWeights;
    }

    public function getAverageWeight(): float
    {
        return $this->averageWeight;
    }

    /**
     * @return Rank[]|null
     */
    public function getRanks(): ?array
    {
        return $this->ranks;
    }
}
