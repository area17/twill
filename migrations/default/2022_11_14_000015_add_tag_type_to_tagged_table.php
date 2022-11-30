<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use A17\Twill\Models\Tag;

class UpdateActivityLogMorphSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('twill.tagged_table', 'tagged');
        $morphClass = (new Tag())->getMorphClass();

        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tag_type')) {
            Schema::table($tableName, function (Blueprint $table) use($morphClass) {
                $table->string('tag_type')->default($morphClass);
                $table->unsignedInteger('position')->default(0);
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
        $tableName = config('twill.tagged_table', 'tagged');

        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tag_type')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['tag_type', 'position']);
            });
        }
    }
}
