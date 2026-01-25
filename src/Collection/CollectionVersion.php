<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

use JanWennrich\BoardGameGeekApi\Common\Version;

final readonly class CollectionVersion
{
    public function __construct(
        private ?int $imageId,
        private ?int $year,
        private ?CollectionVersionPublisher $collectionVersionPublisher,
        private ?string $other,
        private ?string $barcode,
        private ?Version $version,
    ) {}

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getPublisher(): ?CollectionVersionPublisher
    {
        return $this->collectionVersionPublisher;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getItem(): ?Version
    {
        return $this->version;
    }
}
