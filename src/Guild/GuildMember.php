<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

final readonly class GuildMember
{
    public function __construct(
        private string $name,
        private ?\DateTimeImmutable $date,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }
}
