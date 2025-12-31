<?php

namespace JanWennrich\BoardGameGeekApi\Boardgame;

class Expansion extends Link
{
    public function isInbound(): bool
    {
        return ((string) $this->root['inbound']) === 'true';
    }
}
