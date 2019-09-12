<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->string('uuid');
            $table->string('filename')->nullable();
            $table->integer('size')->unsigned();
        });

        Schema::create('fileables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('file_id')->unsigned();
            $table->foreign('file_id', 'fk_files_file_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('fileable_id')->nullable()->unsigned();
            $table->string('fileable_type')->nullable();
            $table->string('role')->nullable();
            $table->string('locale', 6)->index();
            $table->index(['fileable_type', 'fileable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fileables');
        Schema::dropIfExists('files');
    }
}
