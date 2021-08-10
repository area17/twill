<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticleAuthorTable extends Migration
{
    public function up()
    {
        Schema::create('article_author', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('position')->unsigned();
            createDefaultRelationshipTableFields($table, 'article', 'author');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_author');
    }
}
