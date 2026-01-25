<?php

namespace JanWennrich\BoardGameGeekApi\Thread;

final readonly class Thread
{
    /**
     * @param ThreadArticle[] $articles
     */
    public function __construct(
        private int $id,
        private int $numArticles,
        private string $link,
        private string $subject,
        private array $articles,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumArticles(): int
    {
        return $this->numArticles;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return ThreadArticle[]
     */
    public function getArticles(): array
    {
        return $this->articles;
    }
}
