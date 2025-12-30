<?php

namespace JanWennrich\BoardGameGeekApi;

class User
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getLogin(): string
    {
        return (string) $this->root['name'];
    }

    public function getName(): string
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getFirstName(): string
    {
        return (string) $this->root->firstname['value'];
    }

    public function getLastName(): string
    {
        return (string) $this->root->lastname['value'];
    }

    public function getAvatar(): string
    {
        return (string) $this->root->avatarlink['value'];
    }

    public function getCountry(): string
    {
        return (string) $this->root->country['value'];
    }

    public function getYearRegistered(): int
    {
        return (int) $this->root->yearregistered ['value'];
    }
}
