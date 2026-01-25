<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

final readonly class GuildMembers
{
    /**
     * @param GuildMember[] $members
     */
    public function __construct(
        private int $count,
        private int $page,
        private array $members,
    ) {}

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return GuildMember[]
     */
    public function getMembers(): array
    {
        return $this->members;
    }
}
