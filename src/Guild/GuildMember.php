<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

final readonly class GuildMember
{
    public function __construct(
        private string $name,
        private string $date,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
