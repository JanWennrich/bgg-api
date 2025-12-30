<?php

namespace JanWennrich\BoardGameGeekApi;

class HotItem
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getRank(): int
    {
        return (int) $this->root['rank'];
    }

    public function getName(): string
    {
        return (string) $this->root->name['value'];
    }

    public function getYearPublished(): int
    {
        return (int) $this->root->yearpublished['value'];
    }

    public function getThumbnail(): string
    {
        return (string) $this->root->thumbnail['value'];
    }
}
