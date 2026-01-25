<?php

namespace JanWennrich\BoardGameGeekApi\Forum;

final readonly class Forum
{
    /**
     * @param ForumThread[] $threads
     */
    public function __construct(
        private int $id,
        private string $title,
        private int $numThreads,
        private int $numPosts,
        private string $lastPostDate,
        private bool $noPosting,
        private array $threads,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNumThreads(): int
    {
        return $this->numThreads;
    }

    public function getNumPosts(): int
    {
        return $this->numPosts;
    }

    public function getLastPostDate(): string
    {
        return $this->lastPostDate;
    }

    public function isNoPosting(): bool
    {
        return $this->noPosting;
    }

    /**
     * @return ForumThread[]
     */
    public function getThreads(): array
    {
        return $this->threads;
    }
}
