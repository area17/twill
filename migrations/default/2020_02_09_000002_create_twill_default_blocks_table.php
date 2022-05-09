<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillBlocksTable = config('twill.blocks_table', 'twill_blocks');

        if (!Schema::hasTable($twillBlocksTable)) {
            Schema::create($twillBlocksTable, function (Blueprint $table) {
                $table->bigIncrements('id');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $twillBlocksTable = config('twill.blocks_table', 'twill_blocks');

        Schema::dropIfExists($twillBlocksTable);
    }
}
