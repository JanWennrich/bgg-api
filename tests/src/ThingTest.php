<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use JanWennrich\BoardGameGeekApi\Thing;
use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

final class ThingTest extends TestCase
{
    private Thing $thing;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../files/thing.xml');
        $this->thing = new Thing($xml->item);
    }

    public function testGetName(): void
    {
        $this->assertSame('Dream Home', $this->thing->getName());
    }

    public function testGetLinks(): void
    {
        $this->assertCount(16, $this->thing->getLinks());
        $this->assertContainsOnlyInstancesOf(BoardGameGeekApi\Boardgame\Link::class, $this->thing->getLinks());
    }

    public function testGetDesigners(): void
    {
        $items = $this->thing->getBoardgameDesigners();
        $this->assertCount(1, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Designer::class, $item);
        $this->assertSame('Klemens Kalicki', $item->getName());
        $this->assertSame(89488, $item->getId());
    }

    public function testGetCategories(): void
    {
        $items = $this->thing->getBoardgameCategories();
        $this->assertCount(1, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Category::class, $item);
        $this->assertSame('Card Game', $item->getName());
        $this->assertSame(1002, $item->getId());
    }

    public function testGetMechanics(): void
    {
        $items = $this->thing->getBoardgameMechanics();
        $this->assertCount(4, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Mechanic::class, $item);
        $this->assertSame('Card Drafting', $item->getName());
        $this->assertSame(2041, $item->getId());
    }

    public function testGetExpansions(): void
    {
        $items = $this->thing->getBoardgameExpansions();
        $this->assertCount(2, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Expansion::class, $item);
        $this->assertSame('Domek: Promo Token – Car', $item->getName());
        $this->assertSame(208871, $item->getId());
    }

    public function testGetArtists(): void
    {
        $items = $this->thing->getBoardgameArtists();
        $this->assertCount(1, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Artist::class, $item);
        $this->assertSame('Bartłomiej Kordowski', $item->getName());
        $this->assertSame(53716, $item->getId());
    }

    public function testGetPublishers(): void
    {
        $items = $this->thing->getBoardgamePublishers();
        $this->assertCount(7, $items);

        $item = $items[0];
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Publisher::class, $item);
        $this->assertSame('ADC Blackfire Entertainment', $item->getName());
        $this->assertSame(23043, $item->getId());
    }
}
