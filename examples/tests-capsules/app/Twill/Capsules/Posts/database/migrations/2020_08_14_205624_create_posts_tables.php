<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTables extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table
                ->integer('position')
                ->unsigned()
                ->nullable();
        });

        Schema::create('post_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'post');

            $table->string('title', 200)->nullable();

            $table->text('description')->nullable();
        });

        Schema::create('post_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'post');
        });

        Schema::create('post_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'post');
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('post_slugs');
        Schema::dropIfExists('posts');
    }
}
