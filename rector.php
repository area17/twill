<?php

declare(strict_types=1);

use Rector\Core\ValueObject\PhpVersion;
use Rector\Laravel\Set\LaravelSetList;
use Rector\Set\ValueObject\SetList;

/**
 * This rector file is the one used by Twill internally to handle automated upgrades of code.
 */
return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        LaravelSetList::LARAVEL_80
    ]);

    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');

    $rectorConfig->skip([
        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        \Rector\CodingStyle\Rector\If_\NullableCompareToNullRector::class,
        \Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
        \Rector\CodeQuality\Rector\PropertyFetch\ExplicitMethodCallOverMagicGetSetRector::class,
        Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector::class
    ]);
};

