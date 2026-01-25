<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionName
{
    public function __construct(
        private string $value,
        private int $sortIndex,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSortIndex(): int
    {
        return $this->sortIndex;
    }
}
