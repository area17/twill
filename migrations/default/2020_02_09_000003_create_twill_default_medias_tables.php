<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultMediasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillMediasTable = config('twill.medias_table', 'twill_medias');
        $twillMediablesTable = config('twill.mediables_table', 'twill_mediables');

        if (!Schema::hasTable($twillMediasTable)) {
            Schema::create($twillMediasTable, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->softDeletes();
                $table->text('uuid');
                $table->text('alt_text')->nullable();
                $table->integer('width')->unsigned();
                $table->integer('height')->unsigned();
                $table->text('caption')->nullable();
                $table->text('filename')->nullable();
            });
        }

        if (!Schema::hasTable($twillMediablesTable)) {
            Schema::create($twillMediablesTable, function (Blueprint $table) use ($twillMediasTable) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('mediable_id')->nullable()->unsigned();
                $table->string('mediable_type')->nullable();
                $table->bigInteger('media_id')->unsigned();
                $table->integer('crop_x')->nullable();
                $table->integer('crop_y')->nullable();
                $table->integer('crop_w')->nullable();
                $table->integer('crop_h')->nullable();
                $table->string('role')->nullable();
                $table->string('crop')->nullable();
                $table->text('lqip_data')->nullable();
                $table->string('ratio')->nullable();
                $table->json('metadatas');
                $table->foreign('media_id', 'fk_mediables_media_id')->references('id')->on($twillMediasTable)->onDelete('cascade')->onUpdate('cascade');
                $table->index(['mediable_type', 'mediable_id']);
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
        $twillMediasTable = config('twill.medias_table', 'twill_medias');
        $twillMediablesTable = config('twill.mediables_table', 'twill_mediables');

        Schema::dropIfExists($twillMediablesTable);
        Schema::dropIfExists($twillMediasTable);
    }
}
