<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Forum;

final readonly class ForumThread
{
    public function __construct(
        private int $id,
        private string $subject,
        private string $author,
        private int $numArticles,
        private ?\DateTimeImmutable $postDate,
        private ?\DateTimeImmutable $lastPostDate,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getNumArticles(): int
    {
        return $this->numArticles;
    }

    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->postDate;
    }

    public function getLastPostDate(): ?\DateTimeImmutable
    {
        return $this->lastPostDate;
    }
}
