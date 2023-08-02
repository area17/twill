<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTables extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 200)->nullable();
            $table->text('comment')->nullable();
            $table->nullableMorphs('commentable');
            $table->boolean('approved')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}
