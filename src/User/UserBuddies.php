<?php

namespace JanWennrich\BoardGameGeekApi\User;

final readonly class UserBuddies
{
    /**
     * @param UserBuddy[] $buddies
     */
    public function __construct(
        private int $total,
        private int $page,
        private array $buddies,
    ) {}

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return UserBuddy[]
     */
    public function getBuddies(): array
    {
        return $this->buddies;
    }
}
