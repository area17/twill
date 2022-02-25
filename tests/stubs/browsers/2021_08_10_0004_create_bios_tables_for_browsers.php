<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBiosTablesForBrowsers extends Migration
{
    public function up()
    {
        Schema::create('bios', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('writer_id')->nullable();
            $table->foreign('writer_id')->references('id')->on('writers');
        });

        Schema::create('bio_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'bio');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bio_revisions');
        Schema::dropIfExists('bios');
    }
}
