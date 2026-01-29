<?php

namespace JanWennrich\BoardGameGeekApi\Thread;

final readonly class ThreadArticle
{
    public function __construct(
        private int $id,
        private string $username,
        private string $link,
        private ?\DateTimeImmutable $postDate,
        private ?\DateTimeImmutable $editDate,
        private int $numEdits,
        private string $subject,
        private string $body,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->postDate;
    }

    public function getEditDate(): ?\DateTimeImmutable
    {
        return $this->editDate;
    }

    public function getNumEdits(): int
    {
        return $this->numEdits;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
