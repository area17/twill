<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultTagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillTaggedTable = config('twill.tagged_table', 'tagged');

        if (!Schema::hasTable($twillTaggedTable)) {
            Schema::create($twillTaggedTable, function (Blueprint $table) {
                $table->{twillIncrementsMethod()}('id');
                $table->string('taggable_type');
                $table->integer('taggable_id')->unsigned();
                $table->integer('tag_id')->unsigned();
                $table->index(['taggable_type', 'taggable_id']);
            });
        }


        $twillTagsTable = config('twill.tags_table', 'tags');

        if (!Schema::hasTable($twillTagsTable)) {
            Schema::create($twillTagsTable, function (Blueprint $table) {
                $table->{twillIncrementsMethod()}('id');
                $table->string('namespace');
                $table->string('slug');
                $table->string('name');
                $table->integer('count')->default(0)->unsigned();
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
        Schema::dropIfExists(config('twill.tags_table', 'tags'));
        Schema::dropIfExists(config('twill.tagged_table', 'tagged'));
    }
}
