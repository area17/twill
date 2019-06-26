<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->string('uuid');
            $table->string('alt_text');
            $table->integer('width')->unsigned();
            $table->integer('height')->unsigned();
            $table->string('caption')->nullable();
            $table->string('filename')->nullable();
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('mediable_id')->nullable()->unsigned();
            $table->string('mediable_type')->nullable();
            $table->integer('media_id')->unsigned();
            $table->integer('crop_x')->nullable();
            $table->integer('crop_y')->nullable();
            $table->integer('crop_w')->nullable();
            $table->integer('crop_h')->nullable();
            $table->string('role')->nullable();
            $table->string('crop')->nullable();
            $table->text('lqip_data')->nullable();
            $table->string('ratio')->nullable();
            $table->json('metadatas');
            $table->foreign('media_id', 'fk_mediables_media_id')->references('id')->on('medias')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['mediable_type', 'mediable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('medias');
    }
}
