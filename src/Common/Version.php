<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Version
{
    /**
     * @param Name[] $names
     * @param Link[] $links
     */
    public function __construct(
        private int $id,
        private string $type,
        private ?string $thumbnail,
        private ?string $image,
        private array $names,
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

    /**
     * @return Name[]
     */
    public function getNames(): array
    {
        return $this->names;
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
