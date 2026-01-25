<?php

namespace JanWennrich\BoardGameGeekApi\Common;

final readonly class Link
{
    public function __construct(
        private string $type,
        private int $id,
        private string $value,
        private ?bool $inbound,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isInbound(): ?bool
    {
        return $this->inbound;
    }
}
