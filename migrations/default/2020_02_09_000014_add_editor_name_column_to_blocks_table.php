<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditorNameColumnToBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillBlocksTable = config('twill.blocks_table', 'twill_blocks');

        if (Schema::hasTable($twillBlocksTable) && !Schema::hasColumn($twillBlocksTable, 'editor_name')) {
            Schema::table($twillBlocksTable, function (Blueprint $table) {
                $table->string('editor_name', 60)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $twillBlocksTable = config('twill.blocks_table', 'twill_blocks');

        if (Schema::hasTable($twillBlocksTable) && Schema::hasColumn($twillBlocksTable, 'editor_name')) {
            Schema::table($twillBlocksTable, function (Blueprint $table) {
                $table->dropColumn('editor_name');
            });
        }
    }
}
