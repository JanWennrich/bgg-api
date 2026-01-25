<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Unit;

use JanWennrich\BoardGameGeekApi\Collection;
use JanWennrich\BoardGameGeekApi\Collection\CollectionMapper;
use JanWennrich\BoardGameGeekApi\Collection\Item;
use PHPUnit\Framework\TestCase;

final class CollectionMapperTest extends TestCase
{
    private Collection\Collection $collection;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml') ?: $this->fail('Could not load XML file');
        $this->collection = (new CollectionMapper())->fromXml($xml);
    }

    public function testCountMatchesXmlAndIterator(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml') ?: $this->fail('Could not load XML file');
        $expectedTotal = (int) $xml['totalitems'];

        // count() should read from the XML attribute
        $this->assertCount($expectedTotal, $this->collection->getItems());

        // And number of parsed items should match as well
        $itemsFromIterator = iterator_to_array($this->collection->getItems());
        $this->assertCount($expectedTotal, $itemsFromIterator);

        $this->assertContainsOnlyInstancesOf(Collection\CollectionItem::class, $itemsFromIterator);
    }

    public function testFirstItemFields(): void
    {
        $items = $this->collection->getItems();
        $this->assertNotEmpty($items);

        $first = $items[0];

        $this->assertSame('thing', $first->getObjectType());
        $this->assertSame(390092, $first->getObjectId());
        $this->assertSame('boardgame', $first->getSubtype());
        $this->assertSame(113685788, $first->getCollectionId());
        $this->assertSame('¡Aventureros al Tren! Legacy: Leyendas del Oeste', $first->getName()->getValue());
        $this->assertSame("2023", $first->getYearPublished());
        $this->assertStringStartsWith('https://cf.geekdo-images.com/', $first->getImage() ?? '');
        $this->assertStringStartsWith('https://cf.geekdo-images.com/', $first->getThumbnail() ?? '');

        $collectionStatus = $first->getStatus();
        $this->assertTrue($collectionStatus->isOwn());
        $this->assertFalse($collectionStatus->isPrevOwned());
        $this->assertFalse($collectionStatus->isForTrade());
        $this->assertFalse($collectionStatus->isWant());
        $this->assertFalse($collectionStatus->isWantToPlay());
        $this->assertFalse($collectionStatus->isWantToBuy());
        $this->assertFalse($collectionStatus->isWishlist());
        $this->assertFalse($collectionStatus->isPreordered());
        //        $this->assertInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
        //        $this->assertSame('2023-12-18 14:21:07', $itemStatus->getLastModified()->format('Y-m-d H:i:s'));

        $this->assertSame(6, $first->getNumPlays());
    }

    public function testStatsAndRatingsAreNullWhenAbsent(): void
    {
        $items = $this->collection->getItems();
        $this->assertNotEmpty($items);

        $any = $items[0];

        $this->assertNull($any->getStats()?->getMinPlayers());
        $this->assertNull($any->getStats()?->getMaxPlayers());
        $this->assertNull($any->getStats()?->getPlayingTime());
        $this->assertNull($any->getStats()?->getMinPlayTime());
        $this->assertNull($any->getStats()?->getMaxPlayTime());
        $this->assertNull($any->getStats()?->getRating()->getAverage());
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

        $this->assertInstanceOf(Collection\CollectionItem::class, $found, 'Expected to find objectid=359871 in collection.xml');

        $collectionStatus = $found->getStatus();
        $this->assertFalse($collectionStatus->isOwn());
        $this->assertTrue($collectionStatus->isPrevOwned());
        $this->assertSame(1, $found->getNumPlays());
        //        $this->assertInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
        //        $this->assertSame('2025-07-13 15:49:07', $itemStatus->getLastModified()->format('Y-m-d H:i:s'));
    }
}
