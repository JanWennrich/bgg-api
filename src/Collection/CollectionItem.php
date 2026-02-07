<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Collection;

use JanWennrich\BoardGameGeekApi\Thing\ThingType;

final readonly class CollectionItem
{
    public function __construct(
        private int $objectId,
        private ?ThingType $thingType,
        private int $collectionId,
        private string $name,
        private ?string $originalName,
        private ?string $yearPublished,
        private ?string $image,
        private ?string $thumbnail,
        private ?CollectionStats $collectionStats,
        private CollectionStatus $collectionStatus,
        private int $numPlays,
        private ?CollectionPrivateInfo $collectionPrivateInfo,
        private ?CollectionVersion $collectionVersion,
        private ?string $wantPartsList,
        private ?string $hasPartsList,
        private ?string $wishlistComment,
    ) {}

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function getType(): ?ThingType
    {
        return $this->thingType;
    }

    public function getCollectionId(): int
    {
        return $this->collectionId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function getYearPublished(): ?string
    {
        return $this->yearPublished;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getStats(): ?CollectionStats
    {
        return $this->collectionStats;
    }

    public function getStatus(): CollectionStatus
    {
        return $this->collectionStatus;
    }

    public function getNumPlays(): int
    {
        return $this->numPlays;
    }

    public function getPrivateInfo(): ?CollectionPrivateInfo
    {
        return $this->collectionPrivateInfo;
    }

    public function getVersion(): ?CollectionVersion
    {
        return $this->collectionVersion;
    }

    public function getWantPartsList(): ?string
    {
        return $this->wantPartsList;
    }

    public function getHasPartsList(): ?string
    {
        return $this->hasPartsList;
    }

    public function getWishlistComment(): ?string
    {
        return $this->wishlistComment;
    }
}
