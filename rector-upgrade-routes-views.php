<?php

declare(strict_types=1);

use A17\Twill\Rector\RenameRoutes;
use A17\Twill\Rector\RenameViews;
use Rector\Config\RectorConfig;

return static function (RectorConfig $config): void {
    $config->paths([
        getcwd() . '/app/',
        getcwd() . '/resources/',
        getcwd() . '/routes/',
        getcwd() . '/config/',
    ]);

    $config->ruleWithConfiguration(RenameRoutes::class, [
        'path' => getcwd(),
    ]);
    $config->ruleWithConfiguration(RenameViews::class, [
        'path' => getcwd(),
    ]);
};
