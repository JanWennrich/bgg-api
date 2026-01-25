<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionVersionPublisher
{
    public function __construct(
        private string $value,
        private int $publisherId,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPublisherId(): int
    {
        return $this->publisherId;
    }
}
