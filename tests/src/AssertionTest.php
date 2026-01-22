<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use JanWennrich\BoardGameGeekApi\Client;
use JanWennrich\BoardGameGeekApi\ForumListType;
use JanWennrich\BoardGameGeekApi\ItemType;
use JanWennrich\BoardGameGeekApi\Query\CollectionQuery;
use JanWennrich\BoardGameGeekApi\Query\FamilyQuery;
use JanWennrich\BoardGameGeekApi\Query\GuildQuery;
use JanWennrich\BoardGameGeekApi\Query\PlaysQuery;
use JanWennrich\BoardGameGeekApi\Query\SearchQuery;
use JanWennrich\BoardGameGeekApi\Query\ThingQuery;
use JanWennrich\BoardGameGeekApi\Query\ThreadQuery;
use JanWennrich\BoardGameGeekApi\Query\UsersQuery;
use JanWennrich\BoardGameGeekApi\SearchType;
use JanWennrich\BoardGameGeekApi\ThingType;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class AssertionTest extends TestCase
{
    public function testGetThingRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getThing(0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetThingsRejectsEmptyIds(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getThings([]); // @phpstan-ignore argument.type (Testing empty ids assertion.)
    }

    public function testGetThingsRejectsNonPositiveIds(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getThings([0]); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetThingsRejectsTooManyIds(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getThings(range(1, 21));
    }

    public function testGetForumRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getForum(0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetForumRejectsNonPositivePage(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getForum(1, 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetThreadRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getThread(0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetUserRejectsEmptyName(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getUser(''); // @phpstan-ignore argument.type (Testing non-empty string assertion.)
    }

    public function testGetGuildRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getGuild(0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetFamilyRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getFamily(0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetFamiliesRejectsEmptyIds(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getFamilies([]); // @phpstan-ignore argument.type (Testing non-empty ids assertion.)
    }

    public function testGetFamiliesRejectsNonPositiveIds(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getFamilies([0]); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGetCollectionRejectsEmptyUsername(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getCollection(''); // @phpstan-ignore argument.type (Testing non-empty string assertion.)
    }

    public function testSearchRejectsEmptyQuery(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->search(''); // @phpstan-ignore argument.type (Testing non-empty string assertion.)
    }

    public function testGetPlaysForUserRejectsEmptyUsername(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getPlaysForUser(''); // @phpstan-ignore argument.type (Testing non-empty string assertion.)
    }

    public function testGetPlaysForItemRejectsNonPositiveId(): void
    {
        $client = new Client();

        $this->expectException(InvalidArgumentException::class);
        $client->getPlaysForItem(0, ItemType::Thing); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testUsersQueryRejectsNonPositivePage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UsersQuery(page: 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testThingQueryRejectsInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThingQuery(withTypes: ['invalid']); // @phpstan-ignore argument.type (Testing type validation.)
    }

    public function testThingQueryRejectsCommentsAndRatingComments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThingQuery(withComments: true, withRatingComments: true);
    }

    public function testThingQueryRejectsNonPositivePage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThingQuery(page: 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testThingQueryRejectsPageSizeBelowMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThingQuery(pageSize: 9); // @phpstan-ignore argument.type (Testing page size lower bound.)
    }

    public function testThingQueryRejectsPageSizeAboveMaximum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThingQuery(pageSize: 101); // @phpstan-ignore argument.type (Testing page size upper bound.)
    }

    public function testSearchQueryRejectsInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SearchQuery(onlyTypes: ['invalid']); // @phpstan-ignore argument.type (Testing type validation.)
    }

    public function testThreadQueryRejectsNegativeMinArticleId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThreadQuery(minArticleId: -1); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testThreadQueryRejectsCountBelowMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThreadQuery(count: 0); // @phpstan-ignore argument.type (Testing count lower bound.)
    }

    public function testThreadQueryRejectsCountAboveMaximum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThreadQuery(count: 1001); // @phpstan-ignore argument.type (Testing count upper bound.)
    }

    public function testPlaysQueryRejectsNonPositivePage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PlaysQuery(page: 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testGuildQueryRejectsNonPositivePage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GuildQuery(page: 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testFamilyQueryRejectsInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FamilyQuery(withTypes: ['invalid']); // @phpstan-ignore argument.type (Testing type validation.)
    }

    public function testCollectionQueryRejectsEmptyIds(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(ids: []); // @phpstan-ignore argument.type (Testing non-empty ids assertion.)
    }

    public function testCollectionQueryRejectsNonPositiveIds(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(ids: [0]); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testCollectionQueryRejectsWishlistPriorityBelowRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(wishlistPriority: 0); // @phpstan-ignore argument.type (Testing wishlist priority lower bound.)
    }

    public function testCollectionQueryRejectsWishlistPriorityAboveRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(wishlistPriority: 6); // @phpstan-ignore argument.type (Testing wishlist priority upper bound.)
    }

    public function testCollectionQueryRejectsMinPersonalRatingBelowRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(minPersonalRating: 0); // @phpstan-ignore argument.type (Testing rating lower bound.)
    }

    public function testCollectionQueryRejectsMinPersonalRatingAboveRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(minPersonalRating: 11); // @phpstan-ignore argument.type (Testing rating upper bound.)
    }

    public function testCollectionQueryRejectsMaxPersonalRatingBelowRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(maxPersonalRating: 0); // @phpstan-ignore argument.type (Testing rating lower bound.)
    }

    public function testCollectionQueryRejectsMaxPersonalRatingAboveRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(maxPersonalRating: 11); // @phpstan-ignore argument.type (Testing rating upper bound.)
    }

    public function testCollectionQueryRejectsMinBggRatingBelowRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(minBggRating: 0); // @phpstan-ignore argument.type (Testing rating lower bound.)
    }

    public function testCollectionQueryRejectsMinBggRatingAboveRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(minBggRating: 11); // @phpstan-ignore argument.type (Testing rating upper bound.)
    }

    public function testCollectionQueryRejectsMaxBggRatingBelowRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(maxBggRating: 0); // @phpstan-ignore argument.type (Testing rating lower bound.)
    }

    public function testCollectionQueryRejectsMaxBggRatingAboveRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(maxBggRating: 11); // @phpstan-ignore argument.type (Testing rating upper bound.)
    }

    public function testCollectionQueryRejectsNegativeMinPlays(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(minPlays: -1); // @phpstan-ignore argument.type (Testing min plays lower bound.)
    }

    public function testCollectionQueryRejectsNegativeMaxPlays(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(maxPlays: -1); // @phpstan-ignore argument.type (Testing max plays lower bound.)
    }

    public function testCollectionQueryRejectsNonPositiveCollectionId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CollectionQuery(collectionId: 0); // @phpstan-ignore argument.type (Testing positive integer assertion.)
    }

    public function testSearchQueryAcceptsMultipleTypes(): void
    {
        $searchQuery = new SearchQuery(onlyTypes: [SearchType::BoardGame, SearchType::BoardGameExpansion]);
        $this->assertSame([SearchType::BoardGame, SearchType::BoardGameExpansion], $searchQuery->onlyTypes);
    }

    public function testForumListTypeAcceptsValidValue(): void
    {
        $this->assertSame(ForumListType::Thing, ForumListType::from('thing'));
    }
}
