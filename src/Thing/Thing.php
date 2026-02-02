<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Thing;

use JanWennrich\BoardGameGeekApi\Common\Version;
use JanWennrich\BoardGameGeekApi\ThingType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Statistics;
use JanWennrich\BoardGameGeekApi\Common\Video;

final readonly class Thing
{
    /**
     * @param string[] $alternateNames
     * @param Link[] $links
     * @param Poll[] $polls
     * @param Video[] $videos
     * @param Version[] $versions
     * @param Listing[] $marketplaceListings
     */
    public function __construct(
        private int $id,
        private ?ThingType $thingType,
        private ?string $thumbnail,
        private ?string $image,
        private ?string $name,
        private array $alternateNames,
        private ?string $description,
        private ?int $yearPublished,
        private ?\DateTimeImmutable $datePublished,
        private ?int $issueIndex,
        private ?int $minPlayers,
        private ?int $maxPlayers,
        private ?\DateTimeImmutable $releaseDate,
        private ?string $seriesCode,
        private ?int $playingTime,
        private ?int $minPlayTime,
        private ?int $maxPlayTime,
        private ?int $minAge,
        private array $links,
        private array $polls,
        private ?array $videos,
        private array $versions,
        private ?Comments $comments,
        private ?Statistics $statistics,
        private array $marketplaceListings,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): ?ThingType
    {
        return $this->thingType;
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

    public function getPublishYear(): ?int
    {
        return $this->yearPublished;
    }

    public function getExactPublishDate(): ?\DateTimeImmutable
    {
        return $this->datePublished;
    }

    public function getIssueIndex(): ?int
    {
        return $this->issueIndex;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function getSeriesCode(): ?string
    {
        return $this->seriesCode;
    }

    public function getPlayingTime(): ?int
    {
        return $this->playingTime;
    }

    public function getMinPlayTime(): ?int
    {
        return $this->minPlayTime;
    }

    public function getMaxPlayTime(): ?int
    {
        return $this->maxPlayTime;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @return Poll[]
     */
    public function getPolls(): array
    {
        return $this->polls;
    }

    /**
     * @return Video[]|null
     */
    public function getVideos(): ?array
    {
        return $this->videos;
    }

    /**
     * @return Version[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    public function getComments(): ?Comments
    {
        return $this->comments;
    }

    public function getStatistics(): ?Statistics
    {
        return $this->statistics;
    }

    /**
     * @return Listing[]
     */
    public function getMarketplaceListings(): array
    {
        return $this->marketplaceListings;
    }
}
