<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionStats
{
    public function __construct(
        private int $minPlayers,
        private int $maxPlayers,
        private int $minPlayTime,
        private int $maxPlayTime,
        private int $playingTime,
        private int $numOwned,
        private CollectionRating $collectionRating,
    ) {}

    public function getMinPlayers(): int
    {
        return $this->minPlayers;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getMinPlayTime(): int
    {
        return $this->minPlayTime;
    }

    public function getMaxPlayTime(): int
    {
        return $this->maxPlayTime;
    }

    public function getPlayingTime(): int
    {
        return $this->playingTime;
    }

    public function getNumOwned(): int
    {
        return $this->numOwned;
    }

    public function getRating(): CollectionRating
    {
        return $this->collectionRating;
    }
}
