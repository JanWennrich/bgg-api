<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPackage('psr/http-client-implementation', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('psr/http-factory-implementation', [ErrorType::UNUSED_DEPENDENCY]);
