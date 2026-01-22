<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

final class ClientRequestException extends Exception
{
    /**
     * @inheritDoc
     * @param positive-int $attemptNumber Number of the attempt that caused this exception
     * @param int|null $httpCode HTTP response code from the API request. Is {@see null} if no HTTP response code was received.
     */
    public function __construct(
        string $message,
        public readonly int $attemptNumber,
        public readonly ?int $httpCode = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
