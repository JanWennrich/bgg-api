#!/usr/bin/env php
<?php

declare(strict_types=1);

use JanWennrich\BoardGameGeekApi\Test\Console\RecordFixturesCommand;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$recordFixturesCommand = new RecordFixturesCommand();

$application->addCommand($recordFixturesCommand);

if ($recordFixturesCommand->getName() !== null) {
    $application->setDefaultCommand($recordFixturesCommand->getName(), true);
}

$application->run();
