<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

final readonly class Guild
{
    public function __construct(
        private int $id,
        private string $name,
        private string $created,
        private string $category,
        private string $website,
        private string $manager,
        private string $description,
        private GuildLocation $guildLocation,
        private ?GuildMembers $guildMembers,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function getManager(): string
    {
        return $this->manager;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLocation(): GuildLocation
    {
        return $this->guildLocation;
    }

    public function getMembers(): ?GuildMembers
    {
        return $this->guildMembers;
    }
}
