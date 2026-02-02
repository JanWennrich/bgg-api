<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Plays;

final readonly class PlaySubtypeValue
{
    public function __construct(
        private string $value,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }
}
