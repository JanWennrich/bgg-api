<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

final class SearchMapperTest extends TestCase
{
    public function testQuery(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../fixtures/search/search-query=SETI.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $search = (new BoardGameGeekApi\Search\SearchMapper())->fromXml($xml);

        self::assertCount(20, $search->getResults());
        self::assertSame(20, $search->getTotal());
    }
}
