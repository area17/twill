<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorksTables extends Migration
{
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();

            $table->string('video_url')->nullable();
            $table->timestamp('publish_start_date')->nullable();
            $table->boolean('autoplay')->default(false);
            $table->boolean('autoloop')->default(false);
            $table->string('client_name')->nullable();
            $table->integer('year')->nullable();
        });

        Schema::create('work_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'work');
            $table->string('title', 200)->nullable();
            $table->string('subtitle', 200)->nullable();
            $table->text('description')->nullable();
            $table->longtext('case_study_text')->nullable();
        });

        Schema::create('work_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'work');
        });

        Schema::create('work_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'work');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_revisions');
        Schema::dropIfExists('work_translations');
        Schema::dropIfExists('work_slugs');
        Schema::dropIfExists('works');
    }
}
