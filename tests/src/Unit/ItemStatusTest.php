<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Unit;

use JanWennrich\BoardGameGeekApi\Collection\Item;
use PHPUnit\Framework\TestCase;

final class ItemStatusTest extends TestCase
{
    public function testGetStatusReturnsItemStatusWithParsedValues(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-1.xml') ?: $this->fail('Could not load XML file');
        $collectionItem = new Item($xml);

        $itemStatus = $collectionItem->getStatus();

        $this->assertTrue($itemStatus->isOwn());
        $this->assertFalse($itemStatus->isPrevOwned());
        $this->assertFalse($itemStatus->isForTrade());
        $this->assertFalse($itemStatus->isWant());
        $this->assertFalse($itemStatus->isWantToPlay());
        $this->assertFalse($itemStatus->isWantToBuy());
        $this->assertTrue($itemStatus->isWishlist());
        $this->assertSame(4, $itemStatus->getWishlistPriority());
        $this->assertFalse($itemStatus->isPreordered());
        $this->assertInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
        $this->assertSame('2022-03-19 09:58:13', $itemStatus->getLastModified()->format('Y-m-d H:i:s'));
    }

    public function testMissingOrInvalidValuesAreHandled(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-4.xml') ?: $this->fail('Could not load XML file');
        $collectionItem = new Item($xml);

        $itemStatus = $collectionItem->getStatus();

        // All flags default to false when missing
        $this->assertFalse($itemStatus->isOwn());
        $this->assertFalse($itemStatus->isPrevOwned());
        $this->assertFalse($itemStatus->isForTrade());
        $this->assertFalse($itemStatus->isWant());
        $this->assertFalse($itemStatus->isWantToPlay());
        $this->assertFalse($itemStatus->isWantToBuy());
        $this->assertFalse($itemStatus->isWishlist());
        $this->assertFalse($itemStatus->isPreordered());
        // Wishlist priority null when empty
        $this->assertNull($itemStatus->getWishlistPriority());
        // Invalid date becomes null
        $this->assertNotInstanceOf(\DateTimeImmutable::class, $itemStatus->getLastModified());
    }

    public function testCollectionItem2ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-2.xml') ?: $this->fail('Could not load XML file');
        $collectionItem = new Item($xml);

        $itemStatus = $collectionItem->getStatus();

        $this->assertFalse($itemStatus->isOwn());
        $this->assertTrue($itemStatus->isPrevOwned());
        $this->assertFalse($itemStatus->isForTrade());
        $this->assertFalse($itemStatus->isWant());
        $this->assertFalse($itemStatus->isWantToPlay());
        $this->assertFalse($itemStatus->isWantToBuy());
        $this->assertFalse($itemStatus->isWishlist());
        $this->assertFalse($itemStatus->isPreordered());
        $this->assertEquals(new \DateTimeImmutable('2025-07-13 15:49:07'), $itemStatus->getLastModified());
    }

    public function testCollectionItem3ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-3.xml') ?: $this->fail('Could not load XML file');
        $collectionItem = new Item($xml);

        $itemStatus = $collectionItem->getStatus();

        $this->assertTrue($itemStatus->isOwn());
        $this->assertFalse($itemStatus->isPrevOwned());
        $this->assertTrue($itemStatus->isForTrade());
        $this->assertFalse($itemStatus->isWant());
        $this->assertFalse($itemStatus->isWantToPlay());
        $this->assertFalse($itemStatus->isWantToBuy());
        $this->assertFalse($itemStatus->isWishlist());
        $this->assertFalse($itemStatus->isPreordered());
        $this->assertEquals(new \DateTimeImmutable('2025-03-24 16:37:53'), $itemStatus->getLastModified());

    }

    public function testAllCollectionItemsHaveBasicStructure(): void
    {
        $testFiles = [
            'collection-item-1.xml',
            'collection-item-2.xml',
            'collection-item-3.xml',
            'collection-item-4.xml',
        ];

        $collectionItems = array_map(function (string $testFile): Item {
            $xml = simplexml_load_file(__DIR__ . '/../../files/' . $testFile) ?: $this->fail('Could not load XML file');
            return new Item($xml);
        }, $testFiles);

        $this->assertCount(4, $collectionItems);
    }

}
