<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLocaleColumnToTwillDefaultMediables extends Migration
{
    public function up()
    {
        if (Schema::hasTable(config('twill.mediables_table', 'twill_mediables')) && !Schema::hasColumn(config('twill.mediables_table', 'twill_mediables'), 'locale')) {
            Schema::table(config('twill.mediables_table', 'twill_mediables'), function (Blueprint $table) {
                $table->string('locale', 7)->default($this->getCurrentLocale())->index();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable(config('twill.mediables_table', 'twill_mediables')) && Schema::hasColumn(config('twill.mediables_table', 'twill_mediables'), 'locale')) {
            Schema::table(config('twill.mediables_table', 'twill_mediables'), function (Blueprint $table) {
                $table->dropIndex('mediables_locale_index');
                $table->dropColumn('locale');
            });
        }
    }

    public function getCurrentLocale()
    {
        return getLocales()[0] ?? config('app.locale');
    }
}
