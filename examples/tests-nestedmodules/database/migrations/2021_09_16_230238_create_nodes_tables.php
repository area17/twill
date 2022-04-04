<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodesTables extends Migration
{
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table->string('title', 200)->nullable();

            $table->text('description')->nullable();

            $table->integer('position')->unsigned()->nullable();

            $table->nestedSet();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nodes');
    }
}
