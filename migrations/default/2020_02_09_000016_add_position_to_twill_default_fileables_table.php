<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $twillFileablesTable = config('twill.fileables_table', 'twill_fileables');

        if (!Schema::hasColumn($twillFileablesTable, 'position')) {
            Schema::table($twillFileablesTable, function (Blueprint $table) {
                $table->integer('position')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $twillFileablesTable = config('twill.fileables_table', 'twill_fileables');

        Schema::table($twillFileablesTable, function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
