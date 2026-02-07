<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Family;

use JanWennrich\BoardGameGeekApi\Family\FamilyType;
use JanWennrich\BoardGameGeekApi\Common\Link;

final readonly class FamilyItem
{
    /**
     * @param string[] $alternateNames
     * @param Link[] $links
     */
    public function __construct(
        private int $id,
        private ?FamilyType $familyType,
        private ?string $thumbnail,
        private ?string $image,
        private ?string $name,
        private array $alternateNames,
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

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getAlternateNames(): array
    {
        return $this->alternateNames;
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
