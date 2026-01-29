<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Name
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
