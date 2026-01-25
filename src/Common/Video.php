<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Video
{
    public function __construct(
        private int $id,
        private string $title,
        private string $category,
        private string $language,
        private string $link,
        private string $username,
        private int $userId,
        private string $postDate,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPostDate(): string
    {
        return $this->postDate;
    }
}
