<?php

namespace JanWennrich\BoardGameGeekApi\ForumList;

final readonly class ForumListEntry
{
    public function __construct(
        private int $id,
        private int $groupId,
        private string $title,
        private bool $noPosting,
        private string $description,
        private int $numThreads,
        private int $numPosts,
        private string $lastPostDate,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isNoPosting(): bool
    {
        return $this->noPosting;
    }

    public function getDescription(): string
    {
        return $this->description;
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
}
