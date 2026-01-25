<?php

namespace JanWennrich\BoardGameGeekApi\Plays;

final readonly class Play
{
    /**
     * @param PlayPlayer[] $players
     */
    public function __construct(
        private int $id,
        private string $date,
        private int $quantity,
        private int $length,
        private bool $incomplete,
        private bool $nowInStats,
        private string $location,
        private PlayItem $playItem,
        private ?string $comments,
        private array $players,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function isIncomplete(): bool
    {
        return $this->incomplete;
    }

    public function isNowInStats(): bool
    {
        return $this->nowInStats;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getItem(): PlayItem
    {
        return $this->playItem;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @return PlayPlayer[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }
}
