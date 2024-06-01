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
        $tableName = config('twill.related_table', 'twill_related');
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'subject_id')) {
                    $table->string('subject_id', 36)->change();
                }
                if (Schema::hasColumn($tableName, 'related_id')) {
                    $table->string('related_id', 36)->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('twill.related_table', 'twill_related');
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'subject_id')) {
                    $table->bigInteger('subject_id')->change();
                }
                if (Schema::hasColumn($tableName, 'related_id')) {
                    $table->bigInteger('related_id')->change();
                }
            });
        }
    }
};
