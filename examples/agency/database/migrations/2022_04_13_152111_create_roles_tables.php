<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTables extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();

            // add those 2 columns to enable publication timeframe fields (you can use publish_start_date only if you don't need to provide the ability to specify an end date)
            // $table->timestamp('publish_start_date')->nullable();
            // $table->timestamp('publish_end_date')->nullable();
        });

        Schema::create('role_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'role');
            $table->string('title', 200)->nullable();
        });

        Schema::create('role_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'role');
        });


    }

    public function down()
    {

        Schema::dropIfExists('role_translations');
        Schema::dropIfExists('role_slugs');
        Schema::dropIfExists('roles');
    }
}
