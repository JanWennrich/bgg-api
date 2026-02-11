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
        $xml = simplexml_load_file(__DIR__ . '/../../files/thing.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $this->thing = (new Thing\ThingMapper())->fromXml($xml->item[0]);
    }

    public function testGetName(): void
    {
        self::assertSame('Dream Home', $this->thing->getName());
    }

    public function testGetLinks(): void
    {
        self::assertCount(16, $this->thing->getLinks());
        self::assertContainsOnlyInstancesOf(BoardGameGeekApi\Common\Link::class, $this->thing->getLinks());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetDesigners(): void
    {
        self::markTestIncomplete('Boardgame designers are currently not available. They are part of link list');
        //        $items = $this->thing->getLinks();
        //        self::assertCount(1, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Designer::class, $item);
        //        self::assertSame('Klemens Kalicki', $item->getName());
        //        self::assertSame(89488, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetCategories(): void
    {
        self::markTestIncomplete('Categories are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameCategories();
        //        self::assertCount(1, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Category::class, $item);
        //        self::assertSame('Card Game', $item->getName());
        //        self::assertSame(1002, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetMechanics(): void
    {
        self::markTestIncomplete('Mechanics are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameMechanics();
        //        self::assertCount(4, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Mechanic::class, $item);
        //        self::assertSame('Card Drafting', $item->getName());
        //        self::assertSame(2041, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetExpansions(): void
    {
        self::markTestIncomplete('Expansions are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameExpansions();
        //        self::assertCount(2, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Expansion::class, $item);
        //        self::assertSame('Domek: Promo Token – Car', $item->getName());
        //        self::assertSame(208871, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetArtists(): void
    {
        self::markTestIncomplete('Artists are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgameArtists();
        //        self::assertCount(1, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Artist::class, $item);
        //        self::assertSame('Bartłomiej Kordowski', $item->getName());
        //        self::assertSame(53716, $item->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetPublishers(): void
    {
        self::markTestIncomplete('Publishers are currently not available. They are part of link list');

        //        $items = $this->thing->getBoardgamePublishers();
        //        self::assertCount(7, $items);
        //
        //        $item = $items[0];
        //        self::assertInstanceOf(BoardGameGeekApi\Boardgame\Publisher::class, $item);
        //        self::assertSame('ADC Blackfire Entertainment', $item->getName());
        //        self::assertSame(23043, $item->getId());
    }
}
