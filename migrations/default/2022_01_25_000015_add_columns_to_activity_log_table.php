<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = Schema::connection(config('activitylog.database_connection'));
        $tableName = config('activitylog.table_name');

        if ($connection->hasTable($tableName)) {
            $connection->table(
                $tableName,
                function (Blueprint $table) use ($connection, $tableName) {
                    if (!$connection->hasColumn($tableName, 'event')) {
                        $table->string('event')->nullable()->after('subject_type');
                    }

                    if (!$connection->hasColumn($tableName, 'batch_uuid')) {
                        $table->uuid('batch_uuid')->nullable()->after('properties');
                    }
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = Schema::connection(config('activitylog.database_connection'));
        $tableName = config('activitylog.table_name');

        if ($connection->hasTable($tableName)) {
            $connection->table(
                $tableName,
                function (Blueprint $table) use ($connection, $tableName) {
                    if ($connection->hasColumn($tableName, 'event')) {
                        $table->dropColumn('event');
                    }

                    if ($connection->hasColumn($tableName, 'batch_uuid')) {
                        $table->dropColumn('batch_uuid');
                    }
                }
            );
        }
    }
};
