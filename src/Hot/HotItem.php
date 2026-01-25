<?php

namespace JanWennrich\BoardGameGeekApi\Hot;

final readonly class HotItem
{
    public function __construct(
        private int $id,
        private int $rank,
        private ?string $thumbnail,
        private ?string $name,
        private ?int $yearPublished,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }
}
