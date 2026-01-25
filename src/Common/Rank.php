<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Rank
{
    public function __construct(
        private string $type,
        private int $id,
        private string $name,
        private string $friendlyName,
        private int $value,
        private float $bayesAverage,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFriendlyName(): string
    {
        return $this->friendlyName;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getBayesAverage(): float
    {
        return $this->bayesAverage;
    }
}
