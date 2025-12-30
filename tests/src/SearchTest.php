<?php

namespace JanWennrich\BoardGameGeekApi\Test;

use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

class SearchTest extends TestCase
{
    public function testQuery()
    {
        $xml = simplexml_load_file(__DIR__ . '/../files/search.xml');
        $search = new BoardGameGeekApi\Search\Query($xml);
        $this->assertCount(82, $search);
    }
}
