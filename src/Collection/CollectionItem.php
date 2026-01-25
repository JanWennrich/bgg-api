<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

final readonly class CollectionItem
{
    public function __construct(
        private int $objectId,
        private string $objectType,
        private string $subtype,
        private int $collectionId,
        private CollectionName $collectionName,
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

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function getCollectionId(): int
    {
        return $this->collectionId;
    }

    public function getName(): CollectionName
    {
        return $this->collectionName;
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
