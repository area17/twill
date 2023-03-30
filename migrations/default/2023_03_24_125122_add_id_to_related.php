<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn(config('twill.related_table', 'twill_related'), 'id')) {
            Schema::table(config('twill.related_table', 'twill_related'), function (Blueprint $table) {
                // We cannot add id's to existing table with sqlite.
                // For our tests to continue to work, we added this migration in the source file for the original
                // database creation.
                // If your project is running on sqlite you will have to write a custom migration to:
                // - store all the related items in a temporary table
                // - drop the related table
                // - recreate it with the id increments
                // - copy back the data from the temporary table
                // - drop the temporary table.
                if (config('database.default') !== 'sqlite') {
                    $table->increments('id')->first();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('related', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
