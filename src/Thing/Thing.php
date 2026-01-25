<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

use JanWennrich\BoardGameGeekApi\ThingType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Name;
use JanWennrich\BoardGameGeekApi\Common\Statistics;
use JanWennrich\BoardGameGeekApi\Common\Videos;

final readonly class Thing
{
    /**
     * @param Name[] $names
     * @param Link[] $links
     * @param Poll[] $polls
     * @param \JanWennrich\BoardGameGeekApi\Common\Version[] $versions
     * @param Listing[] $marketplaceListings
     */
    public function __construct(
        private int $id,
        private ?ThingType $thingType,
        private ?string $thumbnail,
        private ?string $image,
        private array $names,
        private ?string $description,
        private ?int $yearPublished,
        private ?string $datePublished,
        private ?int $issueIndex,
        private ?int $minPlayers,
        private ?int $maxPlayers,
        private ?string $releaseDate,
        private ?string $seriesCode,
        private ?int $playingTime,
        private ?int $minPlayTime,
        private ?int $maxPlayTime,
        private ?int $minAge,
        private array $links,
        private array $polls,
        private ?Videos $videos,
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

    /**
     * @return Name[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getPrimaryName(): ?Name
    {
        foreach ($this->names as $name) {
            if ($name->getType() === 'primary') {
                return $name;
            }
        }

        return $this->names[0] ?? null;
    }

    /**
     * @return Name[]
     */
    public function getNamesByType(string $type): array
    {
        $names = [];
        foreach ($this->names as $name) {
            if ($name->getType() === $type) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }

    public function getDatePublished(): ?string
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

    public function getReleaseDate(): ?string
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

    public function getVideos(): ?Videos
    {
        return $this->videos;
    }

    /**
     * @return \JanWennrich\BoardGameGeekApi\Common\Version[]
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
