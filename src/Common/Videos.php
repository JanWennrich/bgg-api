<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Videos
{
    /**
     * @param Video[] $videos
     */
    public function __construct(
        private int $total,
        private array $videos,
    ) {}

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return Video[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }
}
