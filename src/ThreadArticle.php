<?php

namespace JanWennrich\BoardGameGeekApi;

class ThreadArticle
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getAuthor(): string
    {
        return (string) $this->root['username'];
    }

    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['postdate']);
    }

    public function getEditDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['editdate']);
    }

    public function getSubject(): string
    {
        return (string) $this->root->subject;
    }

    public function getBody(): string
    {
        return (string) $this->root->body;
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}
