<?php

namespace JanWennrich\BoardGameGeekApi\Search;

class Result
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getType(): string
    {
        return (string) $this->root['type'];
    }

    public function isType(string $type): bool
    {
        return $this->getType() === $type;
    }

    public function getName(): string
    {
        return (string) $this->root->name['value'];
    }

    public function getYearPublished(): int
    {
        return (int) $this->root->yearpublished['value'];
    }
}
