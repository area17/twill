<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkLinksTables extends Migration
{
    public function up()
    {
        Schema::create('work_links', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);

            $table->integer('position')->unsigned()->nullable();
            $table->string('url')->nullable();
            $table->bigInteger('work_id')->nullable();
        });

        Schema::create('work_link_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'work_link');
            $table->string('label')->nullable();
        });
    }

    public function down()
    {

        Schema::dropIfExists('work_link_translations');
        Schema::dropIfExists('work_links');
    }
}
