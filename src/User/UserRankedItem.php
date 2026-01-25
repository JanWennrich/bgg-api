<?php

namespace JanWennrich\BoardGameGeekApi\User;

final readonly class UserRankedItem
{
    public function __construct(
        private int $rank,
        private string $type,
        private int $id,
        private string $name,
    ) {}

    public function getRank(): int
    {
        return $this->rank;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
