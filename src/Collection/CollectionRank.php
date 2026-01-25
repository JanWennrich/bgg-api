<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionRank
{
    public function __construct(
        private string $type,
        private int $id,
        private string $name,
        private string $friendlyName,
        private string $value,
        private string $bayesAverage,
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBayesAverage(): string
    {
        return $this->bayesAverage;
    }
}
