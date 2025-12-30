<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

final class SearchTest extends TestCase
{
    public function testQuery(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../files/search.xml');
        $query = new BoardGameGeekApi\Search\Query($xml);
        $this->assertCount(82, $query);
    }
}
