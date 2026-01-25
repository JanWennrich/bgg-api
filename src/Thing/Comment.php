<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class Comment
{
    public function __construct(
        private string $username,
        private string $rating,
        private string $value,
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRating(): string
    {
        return $this->rating;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
