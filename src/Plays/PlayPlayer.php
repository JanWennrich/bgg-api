<?php

namespace JanWennrich\BoardGameGeekApi\Plays;

final readonly class PlayPlayer
{
    public function __construct(
        private string $username,
        private int $userId,
        private string $name,
        private string $startPosition,
        private string $color,
        private string $score,
        private bool $isNew,
        private int $rating,
        private bool $win,
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartPosition(): string
    {
        return $this->startPosition;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getScore(): string
    {
        return $this->score;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function isWin(): bool
    {
        return $this->win;
    }
}
