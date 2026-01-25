<?php

namespace JanWennrich\BoardGameGeekApi\Plays;

final readonly class Plays
{
    /**
     * @param Play[] $plays
     */
    public function __construct(
        private string $username,
        private int $userId,
        private int $total,
        private int $page,
        private array $plays,
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return Play[]
     */
    public function getPlays(): array
    {
        return $this->plays;
    }
}
