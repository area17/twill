<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBooksTablesForBrowsers extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('book_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'book');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_revisions');
        Schema::dropIfExists('books');
    }
}
