<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('letter_writer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('position')->unsigned();
            createDefaultRelationshipTableFields($table, 'letter', 'writer');
        });
    }

    public function down()
    {
        Schema::dropIfExists('letter_writer');
    }
};
