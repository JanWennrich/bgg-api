<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\ThingType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class CollectionQuery
{
    /**
     * @param bool $withVersions Return version info for each collection item.
     * @param ThingType $onlyThingsWithType Only get items with this type.
     *                                      Note:
     *                                      Default value {@see ThingType::BoardGame} will return items of type {@see ThingType::BoardGame} and {@see ThingType::BoardGameExpansion}.
     *                                      Use "excludeThingWithType" parameter with value {@see ThingType::BoardGameExpansion} and make a second request using "onlyThingsWithType" parameter with value {@see ThingType::BoardGameExpansion}.
     * @param ?ThingType $excludeThingsWithType Exclude items with this type.
     * @param non-empty-array<positive-int>|null $ids Only return item(s) with these IDs.
     * @param bool $onlyBrief Get more abbreviated results.
     * @param bool $withStats Get expanded rating/ranking info.
     * @param ?bool $isOwned Set to "true" to only get owned items. Set to "false" to only get unowned items. Set to "null" to ignore ownership status.
     * @param ?bool $isRated Set to "true" to only get rated items. Set to "false" to only get unrated items. Set to "null" to ignore rating status.
     * @param ?bool $isPlayed Set to "true" to only get played items. Set to "false" to only get unplayed items. Set to "null" to ignore play status.
     * @param ?bool $isCommented Set to "true" to only get commented items. Set to "false" to only get uncommented items. Set to "null" to ignore comment status.
     * @param ?bool $isForTrade Set to "true" to only get items for trade. Set to "false" to only get items not for trade. Set to "null" to ignore trade status.
     * @param ?bool $isWanted Set to "true" to only get wanted items. Set to "false" to only get unwanted items. Set to "null" to ignore wanted status.
     * @param ?bool $isWishlisted Set to "true" to only get wishlisted items. Set to "false" to only get non wishlisted items. Set to "null" to ignore wishlist status.
     * @param int<1,5>|null $wishlistPriority Only get items with this wishlist priority (1-5). Set to "null" to ignore wishlist priority?.
     * @param ?bool $isPreOrdered Set to "true" to only get pre-ordered items. Set to "false" to only get non-pre-ordered items. Set to "null" to ignore pre-order status?.
     * @param ?bool $wantToPlay Set to "true" to only get items wanted to play. Set to "false" to only get items unwanted to play. Set to "null" to ignore wanted to play status?.
     * @param ?bool $wantToBuy Set to "true" to only get items wanted to buy. Set to "false" to only get items unwanted to buy. Set to "null" to ignore wanted to buy status?.
     * @param ?bool $isPreviouslyOwned Set to "true" to only get previously owned items. Set to "false" to only get previously unowned items. Set to "null" to ignore previous ownership status?.
     * @param ?bool $hasParts Set to "true" to only get items with owned parts. Set to "false" to only get items without owned parts. Set to "null" to ignore part ownership status?.
     * @param ?bool $wantParts Set to "true" to only get items with wanted parts. Set to "false" to only get items without wanted parts. Set to "null" to ignore wanted parts status.
     * @param int<1,10>|null $minPersonalRating Only get items with a personal rating above this (1-10).
     * @param int<1,10>|null $maxPersonalRating Only get items with a personal rating below this (1-10).
     * @param int<1,10>|null $minBggRating Only get items with a BoardGameGeek rating above this (1-10).
     * @param int<1,10>|null $maxBggRating Only get items with a BoardGameGeek rating below this (1-10).
     * @param int<0,max>|null $minPlays Only get items with a number of recorded plays above this.
     * @param int<0,max>|null $maxPlays Only get items with a number of recorded plays below this.
     * @param bool $showPrivate Also get private collection info. Only works when retrieving the collection of the currently logged-in user.
     * @param positive-int|null $collectionId Only get items from this collection id.
     * @param ?\DateTimeImmutable $modifiedSince Only return items that were modified since this date and time.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public bool $withVersions = false,
        public ThingType $onlyThingsWithType = ThingType::BoardGame,
        public ?ThingType $excludeThingsWithType = null,
        public ?array $ids = null,
        public bool $onlyBrief = false,
        public bool $withStats = false,
        public ?bool $isOwned = null,
        public ?bool $isRated = null,
        public ?bool $isPlayed = null,
        public ?bool $isCommented = null,
        public ?bool $isForTrade = null,
        public ?bool $isWanted = null,
        public ?bool $isWishlisted = null,
        public ?int $wishlistPriority = null,
        public ?bool $isPreOrdered = null,
        public ?bool $wantToPlay = null,
        public ?bool $wantToBuy = null,
        public ?bool $isPreviouslyOwned = null,
        public ?bool $hasParts = null,
        public ?bool $wantParts = null,
        public ?int $minPersonalRating = null,
        public ?int $maxPersonalRating = null,
        public ?int $minBggRating = null,
        public ?int $maxBggRating = null,
        public ?int $minPlays = null,
        public ?int $maxPlays = null,
        public bool $showPrivate = false,
        public ?int $collectionId = null,
        public ?\DateTimeImmutable $modifiedSince = null,
    ) {
        if ($this->ids !== null) {
            Assert::notEmpty($this->ids);
            Assert::allPositiveInteger($this->ids);
        }

        if ($this->wishlistPriority !== null) {
            Assert::greaterThanEq($this->wishlistPriority, 1);
            Assert::lessThanEq($this->wishlistPriority, 5);
        }

        if ($this->minPersonalRating !== null) {
            Assert::greaterThanEq($this->minPersonalRating, 1);
            Assert::lessThanEq($this->minPersonalRating, 10);
        }

        if ($this->maxPersonalRating !== null) {
            Assert::greaterThanEq($this->maxPersonalRating, 1);
            Assert::lessThanEq($this->maxPersonalRating, 10);
        }

        if ($this->minBggRating !== null) {
            Assert::greaterThanEq($this->minBggRating, 1);
            Assert::lessThanEq($this->minBggRating, 10);
        }

        if ($this->maxBggRating !== null) {
            Assert::greaterThanEq($this->maxBggRating, 1);
            Assert::lessThanEq($this->maxBggRating, 10);
        }

        Assert::nullOrGreaterThanEq($this->minPlays, 0);
        Assert::nullOrGreaterThanEq($this->maxPlays, 0);

        Assert::nullOrPositiveInteger($this->collectionId);
    }
}
