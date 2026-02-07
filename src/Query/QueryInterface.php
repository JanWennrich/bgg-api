<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

interface QueryInterface
{
    /**
     * @internal
     * @return array<string, int|string>
     */
    public function toQueryParameters(): array;
}
