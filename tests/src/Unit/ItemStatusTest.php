<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Unit;

use JanWennrich\BoardGameGeekApi\Collection\Item;
use PHPUnit\Framework\TestCase;

final class ItemStatusTest extends TestCase
{
    public function testGetStatusReturnsItemStatusWithParsedValues(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-1.xml');
        $collectionItem = new Item($xml);

        $sut = $collectionItem->getStatus();

        $this->assertTrue($sut->isOwn());
        $this->assertFalse($sut->isPrevOwned());
        $this->assertFalse($sut->isForTrade());
        $this->assertFalse($sut->isWant());
        $this->assertFalse($sut->isWantToPlay());
        $this->assertFalse($sut->isWantToBuy());
        $this->assertTrue($sut->isWishlist());
        $this->assertSame(4, $sut->getWishlistPriority());
        $this->assertFalse($sut->isPreordered());
        $this->assertNotNull($sut->getLastModified());
        $this->assertSame('2022-03-19 09:58:13', $sut->getLastModified()->format('Y-m-d H:i:s'));
    }

    public function testMissingOrInvalidValuesAreHandled(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-4.xml');
        $collectionItem = new Item($xml);

        $sut = $collectionItem->getStatus();

        // All flags default to false when missing
        $this->assertFalse($sut->isOwn());
        $this->assertFalse($sut->isPrevOwned());
        $this->assertFalse($sut->isForTrade());
        $this->assertFalse($sut->isWant());
        $this->assertFalse($sut->isWantToPlay());
        $this->assertFalse($sut->isWantToBuy());
        $this->assertFalse($sut->isWishlist());
        $this->assertFalse($sut->isPreordered());
        // Wishlist priority null when empty
        $this->assertNull($sut->getWishlistPriority());
        // Invalid date becomes null
        $this->assertNull($sut->getLastModified());
    }

    public function testCollectionItem2ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-2.xml');
        $collectionItem = new Item($xml);

        $sut = $collectionItem->getStatus();

        $this->assertFalse($sut->isOwn());
        $this->assertTrue($sut->isPrevOwned());
        $this->assertFalse($sut->isForTrade());
        $this->assertFalse($sut->isWant());
        $this->assertFalse($sut->isWantToPlay());
        $this->assertFalse($sut->isWantToBuy());
        $this->assertFalse($sut->isWishlist());
        $this->assertFalse($sut->isPreordered());
        $this->assertEquals(new \DateTimeImmutable('2025-07-13 15:49:07'), $sut->getLastModified());
    }

    public function testCollectionItem3ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-3.xml');
        $collectionItem = new Item($xml);

        $sut = $collectionItem->getStatus();

        $this->assertTrue($sut->isOwn());
        $this->assertFalse($sut->isPrevOwned());
        $this->assertTrue($sut->isForTrade());
        $this->assertFalse($sut->isWant());
        $this->assertFalse($sut->isWantToPlay());
        $this->assertFalse($sut->isWantToBuy());
        $this->assertFalse($sut->isWishlist());
        $this->assertFalse($sut->isPreordered());
        $this->assertEquals(new \DateTimeImmutable('2025-03-24 16:37:53'), $sut->getLastModified());

    }

    public function testAllCollectionItemsHaveBasicStructure(): void
    {
        $testFiles = [
            'collection-item-1.xml',
            'collection-item-2.xml',
            'collection-item-3.xml',
            'collection-item-4.xml',
        ];

        $collectionItems = array_map(fn($testFile) => new Item(simplexml_load_file(__DIR__ . '/../../files/' . $testFile)), $testFiles);

        $this->assertCount(4, $collectionItems);
    }

}
