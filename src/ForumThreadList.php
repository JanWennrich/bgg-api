<?php

namespace JanWennrich\BoardGameGeekApi;

class ForumThreadList
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

    public function getPage(): int
    {
        if (isset($this->root->threads['page'])) {
            return (int) $this->root->threads['page'];
        }

        return (int) $this->root['page'];
    }

    /**
     * @return ForumThread[]
     */
    public function getThreads(): array
    {
        $threads = [];

        if (!isset($this->root->threads)) {
            return [];
        }

        $threadsNode = $this->root->threads;
        foreach ($threadsNode->thread as $thread) {
            $threads[] = new ForumThread($thread);
        }

        return $threads;
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
