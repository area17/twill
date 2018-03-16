<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blockable_id')->nullable()->unsigned();
            $table->string('blockable_type')->nullable();
            $table->integer('position')->unsigned();
            $table->json('content');
            $table->string('type');
            $table->string('child_key')->nullable();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->index(['blockable_type', 'blockable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}
