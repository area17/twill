<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTables extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();
            $table->nestedSet();
        });

        Schema::create('category_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'category');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('category_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('category_slugs');
        Schema::dropIfExists('categories');
    }
}
