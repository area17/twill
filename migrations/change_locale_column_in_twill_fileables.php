<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeLocaleColumnInTwillFileables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('fileables') && Schema::hasColumn('fileables', 'locale')) {
            Schema::table('fileables', function (Blueprint $table) {
                $table->string('locale', 7)->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('fileables') && Schema::hasColumn('fileables', 'locale')) {
            Schema::table('fileables', function (Blueprint $table) {
                $table->string('locale', 6)->change();
            });
        }
    }
}
