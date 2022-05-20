<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateTwillDefaultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillSettingsTable = config('twill.settings_table', 'twill_settings');

        if (!Schema::hasTable($twillSettingsTable)) {
            Schema::create($twillSettingsTable, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->softDeletes();
                $table->string('key')->nullable()->index();
                $table->string('section')->nullable()->index();
            });
        }

        if (!Schema::hasTable(Str::singular($twillSettingsTable) . '_translations')) {
            Schema::create(Str::singular($twillSettingsTable) . '_translations', function (Blueprint $table) use ($twillSettingsTable) {
                createDefaultTranslationsTableFields($table, Str::singular($twillSettingsTable));
                $table->text('value')->nullable();
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
        $twillSettingsTable = config('twill.settings_table', 'twill_settings');

        Schema::dropIfExists(Str::singular($twillSettingsTable) . '_translations');
        Schema::dropIfExists($twillSettingsTable);
    }
}
