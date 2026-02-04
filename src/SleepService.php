<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

final class SleepService implements SleepServiceInterface
{
    /**
     * @inheritDoc
     */
    public function sleep(int $seconds): void
    {
        if ($seconds <= 0) {
            return;
        }

        sleep($seconds);
    }
}
