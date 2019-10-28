<?php

use Kalnoy\Nestedset\NestedSet;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTables extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            createDefaultTableFields($table);

            // use this column with the HasPosition trait
            $table
                ->integer('position')
                ->unsigned()
                ->nullable();

            NestedSet::columns($table); // $table->nestedSet(); /// TODO /// This is not working inside tests
        });

        Schema::create('category_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'category');

            $table->string('title', 200)->nullable();
        });

        Schema::create('category_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'category');
        });

        Schema::create('category_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'category');
        });
    }

    public function down()
    {
        /**
         * BUG in the nestedset package
         *
         * TODO
         */
        //        Schema::table('categories', function (Blueprint $table) {
        //            $table->dropNestedSet();
        //        });

        Schema::dropIfExists('category_revisions');

        Schema::dropIfExists('category_translations');

        Schema::dropIfExists('category_slugs');

        Schema::dropIfExists('categories');
    }
}
