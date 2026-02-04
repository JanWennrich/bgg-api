<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

interface SleepServiceInterface
{
    /**
     * Delays the program execution for the given number of seconds
     *
     * @param non-negative-int $seconds
     */
    public function sleep(int $seconds): void;
}
