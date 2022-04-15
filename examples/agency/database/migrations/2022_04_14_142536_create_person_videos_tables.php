<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonVideosTables extends Migration
{
    public function up()
    {
        Schema::create('person_videos', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();
            $table->string('video_url')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('person_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('person_video_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'person_video');
            $table->string('title', 200)->nullable();
        });

        Schema::create('person_video_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'person_video');
        });


    }

    public function down()
    {

        Schema::dropIfExists('person_video_translations');
        Schema::dropIfExists('person_video_slugs');
        Schema::dropIfExists('person_videos');
    }
}
