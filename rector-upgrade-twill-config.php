<?php

declare(strict_types=1);

use A17\Twill\Rector\LegacyTableConfig;
use Rector\Config\RectorConfig;

return static function (RectorConfig $config): void {
    $config->paths([
        getcwd() . '/config/twill.php',
    ]);

    $config->ruleWithConfiguration(LegacyTableConfig::class, [
        'path' => getcwd(),
        'tables' => [
            'blocks_table' => 'blocks',
            'features_table' => 'features',
            'settings_table' => 'settings',
            'medias_table' => 'medias',
            'mediables_table' => 'mediables',
            'files_table' => 'files',
            'fileables_table' => 'fileables',
            'related_table' => 'related',
            'tags_table' => 'tags',
            'tagged_table' => 'tagged',
        ],
    ]);
};
