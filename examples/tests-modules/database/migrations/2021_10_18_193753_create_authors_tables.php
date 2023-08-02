<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorsTables extends Migration
{
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            // feel free to modify the name of this column, but title is supported by default (you would need to specify the name of the column Twill should consider as your "title" column in your module controller if you change it)
            //$table->string('name')->nullable();

            // your generated model and form include a description field, to get you started, but feel free to get rid of it if you don't need it
            // $table->text('description')->nullable();
            $table->foreignIdFor(\App\Models\Category::class)->nullable();

            $table->integer('year')->nullable();
            // $table->text('bio')->nullable();
            $table->date('birthday')->nullable();

            // Featured
            $table->boolean('featured')->default(false);

            // Public
            $table->boolean('public')->default(false);

            // add those 2 colums to enable publication timeframe fields (you can use publish_start_date only if you don't need to provide the ability to specify an end date)
            $table->timestamp('publish_start_date')->nullable();
            $table->timestamp('publish_end_date')->nullable();

            // use this column with the HasPosition trait
            $table
                ->integer('position')
                ->unsigned()
                ->nullable();
        });

        // remove this if you're not going to use any translated field, ie. using the HasTranslation trait. If you do use it, create fields you want translatable in this table instead of the main table above. You do not need to create fields in both tables.
        Schema::create('author_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'author');
            // add some translated fields
            $table->string('name')->nullable();

            $table->text('description')->nullable();

            $table->text('bio')->nullable();
        });

        // remove this if you're not going to use slugs, ie. using the HasSlug trait
        Schema::create('author_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'author');
        });

        // remove this if you're not going to use revisions, ie. using the HasRevisions trait
        Schema::create('author_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'author');
        });

        Schema::create('author_category', function (Blueprint $table) {
            createDefaultRelationshipTableFields($table, 'author', 'category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('author_revisions');

        Schema::dropIfExists('author_translations');

        Schema::dropIfExists('author_slugs');

        Schema::dropIfExists('authors');
    }
}
