<?php

declare(strict_types=1);

use Rector\Set\ValueObject\SetList;

/**
 * This rector file is the one used by Twill internally to handle automated upgrades of code.
 */
return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SetList::DEAD_CODE,
    ]);
    $rectorConfig->import(SetList::TYPE_DECLARATION);
    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::CODING_STYLE);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
};

