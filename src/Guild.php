<?php

namespace JanWennrich\BoardGameGeekApi;

class Guild
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

    public function getDescription(): string
    {
        return (string) $this->root->description;
    }

    public function getLocation(): string
    {
        return (string) $this->root->location;
    }

    public function getMembersPage(): int
    {
        if (isset($this->root->members['page'])) {
            return (int) $this->root->members['page'];
        }

        return (int) $this->root['page'];
    }

    /**
     * @return GuildMember[]
     */
    public function getMembers(): array
    {
        $members = [];

        if (!isset($this->root->members)) {
            return [];
        }

        $membersNode = $this->root->members;
        foreach ($membersNode->member as $member) {
            $members[] = new GuildMember($member);
        }

        return $members;
    }
}
