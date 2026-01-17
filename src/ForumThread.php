<?php

namespace JanWennrich\BoardGameGeekApi;

class ForumThread
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getSubject(): string
    {
        return (string) $this->root['subject'];
    }

    public function getAuthor(): string
    {
        return (string) $this->root['author'];
    }

    public function getNumArticles(): int
    {
        return (int) $this->root['numarticles'];
    }

    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['postdate']);
    }

    public function getLastPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['lastpostdate']);
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
