<?php

namespace JanWennrich\BoardGameGeekApi\User;

final readonly class UserRanking
{
    /**
     * @param UserRankedItem[] $items
     */
    public function __construct(
        private string $domain,
        private array $items,
    ) {}

    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return UserRankedItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
