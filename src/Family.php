<?php

namespace JanWennrich\BoardGameGeekApi;

class Family
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getType(): FamilyType
    {
        return FamilyType::from((string) $this->root['type']);
    }

    public function getName(): string
    {
        return (string) $this->root->name['value'];
    }

    public function getDescription(): string
    {
        return (string) $this->root->description;
    }

    public function getImage(): string
    {
        return (string) $this->root->image;
    }

    public function getThumbnail(): string
    {
        return (string) $this->root->thumbnail;
    }
}
