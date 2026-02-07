<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class ThreadQuery implements QueryInterface
{
    /**
     * @param positive-int|null $minArticleId Filters the results so that only articles with an equal or higher id will be returned.
     * @param ?\DateTimeImmutable $minArticleDate Filters the results so that only articles on or after the specified date/time will be returned.
     * @param int<1,1000>|null $count Limits the number of articles returned to no more than NNN (max 1000).
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public ?int $minArticleId = null,
        public ?\DateTimeImmutable $minArticleDate = null,
        public ?int $count = null,
    ) {
        Assert::nullOrPositiveInteger($this->minArticleId);

        Assert::nullOrGreaterThanEq($this->count, 1);
        Assert::nullOrLessThanEq($this->count, 1000);
    }

    /**
     * @internal
     *
     * @return array{
     *     minarticleid?: positive-int,
     *     minarticledate?: non-empty-string,
     *     count?: int<1,1000>,
     * }
     */
    public function toQueryParameters(): array
    {
        return array_filter([
            'minarticleid' => $this->minArticleId,
            'minarticledate' => $this->minArticleDate?->format('Y-m-d H:i:s'),
            'count' => $this->count,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
