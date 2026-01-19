<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php82: true) // upto PHP 8.2
    ->withSets([
        PHPUnitSetList::PHPUNIT_50,
        PHPUnitSetList::PHPUNIT_60,
        PHPUnitSetList::PHPUNIT_70,
        PHPUnitSetList::PHPUNIT_80,
        PHPUnitSetList::PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_120,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
    )
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withFluentCallNewLine()
    ->withPHPStanConfigs([__DIR__ . '/phpstan.dist.neon'])
    ->withSkip(
        [
            // A switch expression in that file cannot be converted to a match expression as it does not behave the same, for some reason
            ChangeSwitchToMatchRector::class => [__DIR__ . '/src/Boardgame/Link.php'],
            EncapsedStringsToSprintfRector::class,
            WrapEncapsedVariableInCurlyBracesRector::class,
            // remove when entities rely less on XML
            IssetOnPropertyObjectToPropertyExistsRector::class,
            // remove when entities rely less on XML
            ExplicitBoolCompareRector::class,
            // remove when entities rely less on XML
            DisallowedEmptyRuleFixerRector::class,
            // remove when entities rely less on XML
            RemoveUnusedPrivateMethodRector::class => [
                __DIR__ . '/src/Collection/Item.php',
            ],
            // remove when entities rely less on XML
            PrivatizeLocalGetterToPropertyRector::class => [
                __DIR__ . '/src/Collection/Item.php',
            ],
        ],
    );
