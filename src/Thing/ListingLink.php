<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

final readonly class ListingLink
{
    public function __construct(
        private string $href,
        private string $title,
    ) {}

    public function getHref(): string
    {
        return $this->href;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
