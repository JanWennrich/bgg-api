<?php

namespace JanWennrich\BoardGameGeekApi\Search;

final readonly class SearchName
{
    public function __construct(
        private string $type,
        private string $value,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
