<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class UpdateMorphMapReferences extends Command
{
    protected $signature = 'twill:update-morph-references';

    protected $description = 'Update database morph references in Twill tables based on the current morph map configuration';

    public function handle()
    {
        $currentMorphMaps = Relation::morphMap();

        $morphTables = $this->getMorphTables();

        foreach ($morphTables as $table => $columns) {
            foreach (Arr::wrap($columns) as $column) {
                $this->updateMorphReferences($table, $column, $currentMorphMaps);
            }
        }

        $this->info('Database morph references update completed.');
    }

    protected function getMorphTables()
    {
        return [
            config('activitylog.table_name') => 'subject_type',
            config('twill.blocks_table') => 'blockable_type',
            config('twill.fileables_table') => 'fileable_type',
            config('twill.mediables_table') => 'mediable_type',
            config('twill.features_table') => 'featured_type',
            config('twill.related_table') => ['subject_type', 'related_type'],
        ];
    }

    protected function updateMorphReferences($table, $column, $morphMaps)
    {
        foreach ($morphMaps as $alias => $className) {
            $affectedRows = DB::table($table)->where($column, $className)->update([$column => $alias]);

            if ($affectedRows > 0) {
                $this->info("Updated $affectedRows records in '$table' table, changing '$className' to '$alias' in '$column' column.");
            }
        }
    }
}
