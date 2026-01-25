<?php

namespace JanWennrich\BoardGameGeekApi\ForumList;

use JanWennrich\BoardGameGeekApi\ItemType;

final readonly class ForumList
{
    /**
     * @param ForumListEntry[] $forums
     */
    public function __construct(
        private ?ItemType $itemType,
        private int $id,
        private array $forums,
    ) {}

    public function getType(): ?ItemType
    {
        return $this->itemType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ForumListEntry[]
     */
    public function getForums(): array
    {
        return $this->forums;
    }
}
