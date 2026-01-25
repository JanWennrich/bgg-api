<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Ranks
{
    /**
     * @param Rank[] $ranks
     */
    public function __construct(
        private array $ranks,
    ) {}

    /**
     * @return Rank[]
     */
    public function getRanks(): array
    {
        return $this->ranks;
    }
}
