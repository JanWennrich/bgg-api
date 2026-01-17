<?php

namespace JanWennrich\BoardGameGeekApi;

class ForumList
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getType(): ForumListType
    {
        return ForumListType::from((string) $this->root['type']);
    }

    /**
     * @return Forum[]
     */
    public function getForums(): array
    {
        $forums = [];
        foreach ($this->root->forum as $forum) {
            $forums[] = new Forum($forum);
        }

        return $forums;
    }
}
