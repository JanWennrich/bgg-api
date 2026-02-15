<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Thing;

use Webmozart\Assert\Assert;

final readonly class NumberOfPlayersSuggestion
{
    public function __construct(
        /**
         * @var positive-int
         */
        private int $best,
        /**
         * @var positive-int
         */
        private int $minimum,
        /**
         * @var positive-int
         */
        private int $maximum,
    ) {
        Assert::positiveInteger($best);
        Assert::positiveInteger($minimum);
        Assert::positiveInteger($maximum);
    }

    /**
     * @return positive-int
     */
    public function getBest(): int
    {
        return $this->best;
    }

    /**
     * @return positive-int
     */
    public function getMinimum(): int
    {
        return $this->minimum;
    }

    /**
     * @return positive-int
     */
    public function getMaximum(): int
    {
        return $this->maximum;
    }
}
