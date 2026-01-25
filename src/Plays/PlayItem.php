<?php

namespace JanWennrich\BoardGameGeekApi\Plays;

final readonly class PlayItem
{
    /**
     * @param PlaySubtypeValue[] $subtypes
     */
    public function __construct(
        private string $name,
        private string $objectType,
        private int $objectId,
        private array $subtypes,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @return PlaySubtypeValue[]
     */
    public function getSubtypes(): array
    {
        return $this->subtypes;
    }
}
