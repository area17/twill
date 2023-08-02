<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('menu_links', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table->string('title', 200)->nullable();

            $table->text('description')->nullable();

            $table->integer('position')->unsigned()->nullable();

            $table->nestedSet();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_links');
    }
};
