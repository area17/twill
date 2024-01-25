<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable(config('twill.fileables_table', 'twill_fileables')) && Schema::hasColumn(config('twill.fileables_table', 'twill_fileables'), 'locale')) {
            Schema::table(config('twill.fileables_table', 'twill_fileables'), function (Blueprint $table) {
                $table->string('locale', 7)->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable(config('twill.fileables_table', 'twill_fileables')) && Schema::hasColumn(config('twill.fileables_table', 'twill_fileables'), 'locale')) {
            Schema::table(config('twill.fileables_table', 'twill_fileables'), function (Blueprint $table) {
                $table->string('locale', 6)->change();
            });
        }
    }
};
