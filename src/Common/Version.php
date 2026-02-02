<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Version
{
    /**
     * @param string[] $alternateNames
     * @param Link[] $links
     */
    public function __construct(
        private int $id,
        private string $type,
        private ?string $thumbnail,
        private ?string $image,
        private ?string $name,
        private array $alternateNames,
        private ?int $yearPublished,
        private array $links,
        private ?string $productCode,
        private ?float $width,
        private ?float $length,
        private ?float $depth,
        private ?float $weight,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function getDepth(): ?float
    {
        return $this->depth;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }
}
