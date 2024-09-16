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
        $twillMediablesTable = config('twill.mediables_table', 'twill_mediables');

        Schema::table($twillMediablesTable, function (Blueprint $table) {
            $table->integer('position')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $twillMediablesTable = config('twill.mediables_table', 'twill_mediables');

        Schema::table($twillMediablesTable, function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
