<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class RetryConfig
{
    /**
     * @param positive-int $maxAttempts
     * @param positive-int $initialExponentialRetryDelayInSeconds
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public int $maxAttempts = 3,
        public int $initialExponentialRetryDelayInSeconds = 1,
        public bool $retryOnQueuedRequest = true,
        public bool $retryOnServerError = true,
        public bool $retryOnTransportErrors = true,
        public bool $retryOnInvalidXml = true,
    ) {
        Assert::positiveInteger($this->maxAttempts);
        Assert::positiveInteger($this->initialExponentialRetryDelayInSeconds);
    }
}
