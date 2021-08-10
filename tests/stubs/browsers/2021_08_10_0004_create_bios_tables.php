<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBiosTables extends Migration
{
    public function up()
    {
        Schema::create('bios', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('author_id')->nullable()->onUpdate('cascade')->onDelete('cascade');
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
