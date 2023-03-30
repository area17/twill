<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillRelatedTable = config('twill.related_table', 'twill_related');

        if (!Schema::hasTable($twillRelatedTable)) {
            Schema::create($twillRelatedTable, function (Blueprint $table) {
                $table->increments('id');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $twillRelatedTable = config('twill.related_table', 'twill_related');

        Schema::dropIfExists($twillRelatedTable);
    }
}
