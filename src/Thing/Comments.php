<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class Comments
{
    /**
     * @param Comment[] $comments
     */
    public function __construct(
        private string $page,
        private int $totalItems,
        private array $comments,
    ) {}

    public function getPage(): string
    {
        return $this->page;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }
}
