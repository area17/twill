<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTables extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            createDefaultTableFields($table);

            $table->string('title');
            $table->foreignIdFor(\App\Models\Project::class)->nullable();
            $table->string('url');

            $table->integer('position')->unsigned()->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
}
