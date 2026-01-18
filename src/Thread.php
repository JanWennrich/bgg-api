<?php

namespace JanWennrich\BoardGameGeekApi;

class Thread
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getSubject(): string
    {
        $subject = (string) $this->root['subject'];
        if ($subject === '' && isset($this->root->subject)) {
            return (string) $this->root->subject;
        }

        return $subject;
    }

    public function getAuthor(): string
    {
        $author = (string) $this->root['author'];
        if ($author === '' && isset($this->root->author)) {
            return (string) $this->root->author;
        }

        return $author;
    }

    public function getNumArticles(): int
    {
        return (int) $this->root['numarticles'];
    }

    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['postdate']);
    }

    public function getLastPostDate(): ?\DateTimeImmutable
    {
        return $this->parseDate((string) $this->root['lastpostdate']);
    }

    /**
     * @return ThreadArticle[]
     */
    public function getArticles(): array
    {
        $articles = [];

        if (!isset($this->root->articles)) {
            return [];
        }

        $articlesNode = $this->root->articles;
        foreach ($articlesNode->article as $article) {
            $articles[] = new ThreadArticle($article);
        }

        return $articles;
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
