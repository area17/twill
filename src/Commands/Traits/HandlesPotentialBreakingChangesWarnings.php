<?php

namespace A17\Twill\Commands\Traits;

use Illuminate\Support\Facades\Schema;

/* @mixin \Illuminate\Console\Command */
trait HandlesPotentialBreakingChangesWarnings
{
    public function warnAboutNewPositionColumns(): void
    {
        if (config('twill.load_default_migrations', true)) {
            return;
        }

        $mediablesHasPosition = Schema::hasColumn(config('twill.mediables_table', 'twill_mediables'), 'position');
        $fileablesHasPosition = Schema::hasColumn(config('twill.fileables_table', 'twill_fileables'), 'position');

        if ($mediablesHasPosition && $fileablesHasPosition) {
            return;
        }

        $this->warn('⚠️  Twill 3.5.0 introduced 2 new database migrations to fix a bug with the medias and files fields position management, make sure to add them to your project.');
        $this->warn("\nAdd position field to the twill_mediables table:");
        $this->info("🔗 https://github.com/area17/twill/blob/3.5.0/migrations/default/2020_02_09_000015_add_position_to_twill_default_mediables_table.php");
        $this->warn("\nAdd position field to the twill_fileables table:");
        $this->info("🔗 https://github.com/area17/twill/blob/3.5.0/migrations/default/2020_02_09_000016_add_position_to_twill_default_fileables_table.php");
    }
}
