<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLettersTablesForBrowsers extends Migration
{
    public function up()
    {
        Schema::create('letters', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('letter_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'letter');
        });
    }

    public function down()
    {
        Schema::dropIfExists('letter_revisions');
        Schema::dropIfExists('letters');
    }
}
