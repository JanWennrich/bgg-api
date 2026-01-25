<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use JanWennrich\BoardGameGeekApi\Thing;
use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

final class ThingMapperTest extends TestCase
{
    private Thing\Thing $thing;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../files/thing.xml') ?: $this->fail('Could not load XML file');
        $this->thing = (new Thing\ThingMapper())->fromXml($xml->item[0]);
    }

    public function testGetName(): void
    {
        $this->assertSame('Dream Home', $this->thing->getPrimaryName()?->getValue());
    }

    public function testGetLinks(): void
    {
        $this->assertCount(16, $this->thing->getLinks());
        $this->assertContainsOnlyInstancesOf(BoardGameGeekApi\Common\Link::class, $this->thing->getLinks());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetDesigners(): void
    {
        $this->markTestSkipped('Boardgame designers are currently not available. They are part of link list');
        //        $items = $this->thing->getLinks();
        //        $this->assertCount(1, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Designer::class, $item);
        //        $this->assertSame('Klemens Kalicki', $item->getName());
        //        $this->assertSame(89488, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetCategories(): void
    {
        $this->markTestSkipped('Categories are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameCategories();
        //        $this->assertCount(1, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Category::class, $item);
        //        $this->assertSame('Card Game', $item->getName());
        //        $this->assertSame(1002, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetMechanics(): void
    {
        $this->markTestSkipped('Mechanics are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameMechanics();
        //        $this->assertCount(4, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Mechanic::class, $item);
        //        $this->assertSame('Card Drafting', $item->getName());
        //        $this->assertSame(2041, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetExpansions(): void
    {
        $this->markTestSkipped('Expansions are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameExpansions();
        //        $this->assertCount(2, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Expansion::class, $item);
        //        $this->assertSame('Domek: Promo Token – Car', $item->getName());
        //        $this->assertSame(208871, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetArtists(): void
    {
        $this->markTestSkipped('Artists are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameArtists();
        //        $this->assertCount(1, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Artist::class, $item);
        //        $this->assertSame('Bartłomiej Kordowski', $item->getName());
        //        $this->assertSame(53716, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetPublishers(): void
    {
        $this->markTestSkipped('Publishers are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgamePublishers();
        //        $this->assertCount(7, $items);
        //
        //        $item = $items[0];
        //        $this->assertInstanceOf(BoardGameGeekApi\Boardgame\Publisher::class, $item);
        //        $this->assertSame('ADC Blackfire Entertainment', $item->getName());
        //        $this->assertSame(23043, $item->getId());
    }
}
