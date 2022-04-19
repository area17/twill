<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultFilesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillFilesTable = config('twill.files_table', 'twill_files');
        $twillFileablesTable = config('twill.fileables_table', 'twill_fileables');

        if (!Schema::hasTable($twillFilesTable)) {
            Schema::create($twillFilesTable, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->softDeletes();
                $table->text('uuid');
                $table->text('filename')->nullable();
                $table->integer('size')->unsigned();
            });
        }

        if (!Schema::hasTable($twillFileablesTable)) {
            Schema::create($twillFileablesTable, function (Blueprint $table) use ($twillFilesTable) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('file_id')->unsigned();
                $table->foreign('file_id', 'fk_files_file_id')->references('id')->on($twillFilesTable)->onDelete('cascade')->onUpdate('cascade');
                $table->bigInteger('fileable_id')->nullable()->unsigned();
                $table->string('fileable_type')->nullable();
                $table->string('role')->nullable();
                $table->string('locale', 6)->index();
                $table->index(['fileable_type', 'fileable_id']);
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
        $twillFilesTable = config('twill.files_table', 'twill_files');
        $twillFileablesTable = config('twill.fileables_table', 'twill_fileables');

        Schema::dropIfExists($twillFileablesTable);
        Schema::dropIfExists($twillFilesTable);
    }
}
