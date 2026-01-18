<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\PlayType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class PlaysQuery
{
    /**
     * @param ?\DateTimeImmutable $minDate Returns only plays of the specified date or later.
     * @param ?\DateTimeImmutable $maxDate Returns only plays of the specified date or earlier.
     * @param PlayType $playType Limits play results to the specified play type
     * @param positive-int $page The page of information to request.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public ?\DateTimeImmutable $minDate = null,
        public ?\DateTimeImmutable $maxDate = null,
        public PlayType $playType = PlayType::BoardGame,
        public int $page = 1,
    ) {
        Assert::positiveInteger($this->page);
    }
}
