<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Unit;

use JanWennrich\BoardGameGeekApi\Collection;
use JanWennrich\BoardGameGeekApi\Collection\CollectionMapper;
use JanWennrich\BoardGameGeekApi\Collection\Item;
use JanWennrich\BoardGameGeekApi\Thing\ThingType;
use PHPUnit\Framework\TestCase;

final class CollectionMapperTest extends TestCase
{
    private Collection\Collection $collection;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $this->collection = (new CollectionMapper())->fromXml($xml);
    }

    public function testCountMatchesXmlAndIterator(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $expectedTotal = (int) $xml['totalitems'];

        // count() should read from the XML attribute
        self::assertCount($expectedTotal, $this->collection->getItems());

        // And number of parsed items should match as well
        $itemsFromIterator = iterator_to_array($this->collection->getItems());
        self::assertCount($expectedTotal, $itemsFromIterator);

        self::assertContainsOnlyInstancesOf(Collection\CollectionItem::class, $itemsFromIterator);
    }

    public function testFirstItemFields(): void
    {
        $items = $this->collection->getItems();
        self::assertNotEmpty($items);

        $first = $items[0];

        self::assertSame(390092, $first->getObjectId());
        self::assertSame(ThingType::BoardGame, $first->getType());
        self::assertSame(113685788, $first->getCollectionId());
        self::assertSame('¡Aventureros al Tren! Legacy: Leyendas del Oeste', $first->getName());
        self::assertSame("2023", $first->getYearPublished());
        self::assertStringStartsWith('https://cf.geekdo-images.com/', $first->getImage() ?? '');
        self::assertStringStartsWith('https://cf.geekdo-images.com/', $first->getThumbnail() ?? '');

        $collectionStatus = $first->getStatus();
        self::assertTrue($collectionStatus->isOwn());
        self::assertFalse($collectionStatus->isPrevOwned());
        self::assertFalse($collectionStatus->isForTrade());
        self::assertFalse($collectionStatus->isWant());
        self::assertFalse($collectionStatus->isWantToPlay());
        self::assertFalse($collectionStatus->isWantToBuy());
        self::assertFalse($collectionStatus->isWishlist());
        self::assertFalse($collectionStatus->isPreordered());
        //        self::assertInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
        //        self::assertSame('2023-12-18 14:21:07', $itemStatus->getLastModified()->format('Y-m-d H:i:s'));

        self::assertSame(6, $first->getNumPlays());
    }

    public function testStatsAndRatingsAreNullWhenAbsent(): void
    {
        $items = $this->collection->getItems();
        self::assertNotEmpty($items);

        $any = $items[0];

        self::assertNull($any->getStats()?->getMinPlayers());
        self::assertNull($any->getStats()?->getMaxPlayers());
        self::assertNull($any->getStats()?->getPlayingTime());
        self::assertNull($any->getStats()?->getMinPlayTime());
        self::assertNull($any->getStats()?->getMaxPlayTime());
        self::assertNull($any->getStats()?->getRating()->getAverage());
    }

    public function testPrevOwnedItemExistsAndParsed(): void
    {
        $targetId = 359871; // Arcs
        $found = null;
        foreach ($this->collection->getItems() as $collectionItem) {
            if ($collectionItem->getObjectId() === $targetId) {
                $found = $collectionItem;
                break;
            }
        }

        self::assertInstanceOf(Collection\CollectionItem::class, $found, 'Expected to find objectid=359871 in collection.xml');

        $collectionStatus = $found->getStatus();
        self::assertFalse($collectionStatus->isOwn());
        self::assertTrue($collectionStatus->isPrevOwned());
        self::assertSame(1, $found->getNumPlays());
        //        self::assertInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
        //        self::assertSame('2025-07-13 15:49:07', $itemStatus->getLastModified()->format('Y-m-d H:i:s'));
    }
}
