<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLocaleColumnToTwillMediables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('mediables') && !Schema::hasColumn('mediables', 'locale')) {
            Schema::table('mediables', function (Blueprint $table) {
                $table->string('locale', 7)->index();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('mediables') && Schema::hasColumn('mediables', 'locale')) {
            Schema::table('mediables', function (Blueprint $table) {
                $table->dropIndex('mediables_locale_index');
                $table->dropColumn('locale');
            });
        }
    }
}
