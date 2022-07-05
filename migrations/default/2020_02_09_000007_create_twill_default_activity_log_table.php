<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable(config('activitylog.table_name'))) {
            Schema::create(config('activitylog.table_name'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->integer('subject_id')->nullable();
                $table->string('subject_type')->nullable();
                $table->integer('causer_id')->nullable();
                $table->string('causer_type')->nullable();
                $table->text('properties')->nullable();
                $table->timestamps();

                $table->index('log_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('activitylog.table_name'));
    }
}
