<?php

namespace JanWennrich\BoardGameGeekApi\User;

final readonly class UserGuilds
{
    /**
     * @param UserGuild[] $guilds
     */
    public function __construct(
        private int $total,
        private int $page,
        private array $guilds,
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
     * @return UserGuild[]
     */
    public function getGuilds(): array
    {
        return $this->guilds;
    }
}
