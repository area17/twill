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
                    if ($connection->hasColumn($tableName, 'subject_id')) {
                        $table->bigInteger('subject_id')->change();
                    }

                    if ($connection->hasColumn($tableName, 'causer_id')) {
                        $table->bigInteger('causer_id')->change();
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
                    if ($connection->hasColumn($tableName, 'subject_id')) {
                        $table->integer('subject_id')->change();
                    }

                    if ($connection->hasColumn($tableName, 'causer_id')) {
                        $table->integer('causer_id')->change();
                    }
                }
            );
        }
    }
};
