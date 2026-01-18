<?php

namespace JanWennrich\BoardGameGeekApi;

class GuildMember
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getName(): string
    {
        return (string) $this->root['name'];
    }

    public function getJoinDate(): ?\DateTimeImmutable
    {
        $joinDate = (string) $this->root['joindate'];
        if ($joinDate === '') {
            $joinDate = (string) $this->root['date'];
        }

        return $this->parseDate($joinDate);
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}
