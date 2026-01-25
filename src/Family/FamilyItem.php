<?php

namespace JanWennrich\BoardGameGeekApi\Family;

use JanWennrich\BoardGameGeekApi\FamilyType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Name;

final readonly class FamilyItem
{
    /**
     * @param Name[] $names
     * @param Link[] $links
     */
    public function __construct(
        private int $id,
        private ?FamilyType $familyType,
        private ?string $thumbnail,
        private ?string $image,
        private array $names,
        private ?string $description,
        private array $links,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): ?FamilyType
    {
        return $this->familyType;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return Name[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
