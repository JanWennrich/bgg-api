<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\GuildMemberSort;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class GuildQuery
{
    /**
     * @param bool $withMembers Include member roster in the results.
     * @param GuildMemberSort $sort Specify how to sort the members list.
     * @param positive-int $page The page of the members list to return.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public bool $withMembers = false,
        public GuildMemberSort $sort = GuildMemberSort::Username,
        public int $page = 1,
    ) {
        Assert::positiveInteger($this->page);
    }
}
