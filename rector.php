<?php

declare(strict_types=1);

use Rector\Set\ValueObject\SetList;

/**
 * This rector file is the one used by Twill internally to handle automated upgrades of code.
 */
return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
};

