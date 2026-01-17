<?php

namespace JanWennrich\BoardGameGeekApi;

class Forum
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getTitle(): string
    {
        return (string) $this->root['title'];
    }

    public function getNumThreads(): int
    {
        return (int) $this->root['numthreads'];
    }

    public function getNumPosts(): int
    {
        return (int) $this->root['numposts'];
    }

    public function getLastPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['lastpostdate']);
    }

    public function getLastPostId(): int
    {
        return (int) $this->root['lastpostid'];
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
