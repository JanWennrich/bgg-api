<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class RetryConfig
{
    /**
     * @param positive-int $maxAttempts
     * @param int<0,max> $delayInSeconds
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public int $maxAttempts = 3,
        public int $delayInSeconds = 5,
        public bool $retryOnQueuedRequest = true,
        public bool $retryOnServerError = true,
        public bool $retryOnTransportErrors = true,
        public bool $retryOnInvalidXml = true,
    ) {
        Assert::positiveInteger($this->maxAttempts);
        Assert::greaterThanEq($this->delayInSeconds, 0);
    }
}
