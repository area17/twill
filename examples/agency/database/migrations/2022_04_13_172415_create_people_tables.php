<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTables extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->integer('start_year')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();


            // add those 2 columns to enable publication timeframe fields (you can use publish_start_date only if you don't need to provide the ability to specify an end date)
            // $table->timestamp('publish_start_date')->nullable();
            // $table->timestamp('publish_end_date')->nullable();
        });

        Schema::create('person_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'person');
            $table->string('full_name', 200)->nullable();
            $table->text('biography')->nullable();
        });

        Schema::create('person_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'person');
        });

        Schema::create('person_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'person');
        });
    }

    public function down()
    {
        Schema::dropIfExists('person_revisions');
        Schema::dropIfExists('person_translations');
        Schema::dropIfExists('person_slugs');
        Schema::dropIfExists('people');
    }
}
