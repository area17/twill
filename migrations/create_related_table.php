<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related', function (Blueprint $table) {
            $table->integer('subject_id')->nullable()->unsigned();
            $table->string('subject_type', 255);
            $table->integer('related_id')->nullable()->unsigned();
            $table->string('related_type', 255);
            $table->string('browser_name')->index();
            $table->integer('position')->unsigned();

            $table->unique(
                ['subject_id', 'subject_type', 'related_id', 'related_type', 'browser_name'],
                'related_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related');
    }
}
