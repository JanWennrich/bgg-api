<?php

namespace JanWennrich\BoardGameGeekApi;

class Play
{
    public function __construct(private \SimpleXMLElement $root) {}

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getDate(): string
    {
        return (string) $this->root['date'];
    }

    public function getQuantity(): int
    {
        return (int) $this->root['quantity'];
    }

    public function getLength(): int
    {
        return (int) $this->root['length'];
    }

    public function isIncomplete(): bool
    {
        return $this->toBool($this->root['incomplete'] ?? null);
    }

    public function isNoWinStats(): bool
    {
        return $this->toBool($this->root['nowinstats'] ?? null);
    }

    public function getLocation(): string
    {
        return (string) $this->root['location'];
    }

    public function getObjectType(): string
    {
        return (string) $this->root->item['objecttype'];
    }

    public function getObjectId(): int
    {
        return (int) $this->root->item['objectid'];
    }

    public function getObjectName(): string
    {
        return (string) $this->root->item['name'];
    }

    /**
     * @return string[]
     */
    public function getSubtypes(): array
    {
        $subtypes = [];
        if (isset($this->root->item->subtypes)) {
            foreach ($this->root->item->subtypes->subtype as $subtype) {
                $subtypes[] = (string) $subtype['value'];
            }
        }

        return $subtypes;
    }

    public function getComments(): string
    {
        return isset($this->root->comments) ? (string) $this->root->comments : '';
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        $players = [];
        if (isset($this->root->players)) {
            foreach ($this->root->players->player as $player) {
                $players[] = new Player($player);
            }
        }

        return $players;
    }

    private function toBool($value): bool
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
