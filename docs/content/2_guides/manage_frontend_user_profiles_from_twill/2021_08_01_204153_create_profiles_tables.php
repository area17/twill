<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfilesTables extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table->string('name', 200)->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_vip')->default(false);

            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
