<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTables extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            createDefaultTableFields($table);
        });

        Schema::create('project_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'project');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('project_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'project');
        });

        Schema::create('project_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'project');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_revisions');
        Schema::dropIfExists('project_translations');
        Schema::dropIfExists('project_slugs');
        Schema::dropIfExists('projects');
    }
}
