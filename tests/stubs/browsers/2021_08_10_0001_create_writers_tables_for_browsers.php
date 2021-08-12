<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWritersTablesForBrowsers extends Migration
{
    public function up()
    {
        Schema::create('writers', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('writer_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'writer');
        });
    }

    public function down()
    {
        Schema::dropIfExists('writer_revisions');
        Schema::dropIfExists('writers');
    }
}
