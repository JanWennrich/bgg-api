<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\UserDomain;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class UsersQuery
{
    /**
     * @param bool $withBuddies Turns on optional buddies reporting. Results are paged; see page parameter.
     * @param bool $withGuilds Turns on optional guilds reporting. Results are paged; see page parameter.
     * @param bool $withHot Include the user's hot 10 list from their profile.
     * @param bool $withTop Include the user's top 10 list from their profile.
     * @param UserDomain $domain Controls the domain for the users hot 10 and top 10 lists.
     * @param positive-int $page Specifies the page of buddy and guild results to return.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public bool $withBuddies = false,
        public bool $withGuilds = false,
        public bool $withHot = false,
        public bool $withTop = false,
        public UserDomain $domain = UserDomain::BoardGame,
        public int $page = 1,
    ) {
        Assert::positiveInteger($this->page);
    }
}
