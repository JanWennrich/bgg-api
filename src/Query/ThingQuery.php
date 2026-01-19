<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\ThingType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class ThingQuery
{
    /**
     * @param ThingType[] $withTypes Specify that, regardless of the type of thing asked for by id, the results are filtered by the {@see ThingType}(s) specified. Multiple {@see ThingType}s can be specified.
     * @param bool $withVersions Return version info for the item(s).
     * @param bool $withVideos Return videos for the item(s)
     * @param bool $withStats Return ranking and rating stats for the item(s).
     * @param bool $withMarketplaceData Returns marketplace data.
     * @param bool $withComments Return all comments about the item(s). Also includes ratings when commented. See page parameter.
     * @param bool $withRatingComments Return all ratings for the item(s). Also includes comments when rated. See page parameter. The $withRatingComments and $withComments parameters cannot be used together. Ratings are sorted in ascending rating value.
     * @param positive-int $page Controls the page of data to see for historical info, comments, and ratings data.
     * @param int<10, 100> $pageSize Set the number of records to return in paging. Minimum is 10, maximum is 100.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public array $withTypes = [],
        public bool $withVersions = false,
        public bool $withVideos = false,
        public bool $withStats = false,
        public bool $withMarketplaceData = false,
        public bool $withComments = false,
        public bool $withRatingComments = false,
        public int $page = 1,
        public int $pageSize = 10,
    ) {
        Assert::allIsInstanceOf($this->withTypes, ThingType::class);

        if ($this->withComments) {
            Assert::false($this->withRatingComments);
        }

        if ($this->withRatingComments) {
            Assert::false($this->withComments);
        }

        Assert::positiveInteger($this->page);

        Assert::greaterThanEq($this->pageSize, 10);
        Assert::lessThanEq($this->pageSize, 100);
    }
}
