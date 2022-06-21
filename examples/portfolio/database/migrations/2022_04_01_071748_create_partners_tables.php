<?php

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTables extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            createDefaultTableFields($table);
        });

        Schema::create('partner_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'partner');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('partner_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'partner');
        });

        Schema::create('partner_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'partner');
        });

        Schema::create('partner_project', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignIdFor(Partner::class);
            $table->foreignIdFor(Project::class);
            $table->json('role')->nullable();
            $table->integer('position')->default(999);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_revisions');
        Schema::dropIfExists('partner_translations');
        Schema::dropIfExists('partner_slugs');
        Schema::dropIfExists('partners');
    }
}
