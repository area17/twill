<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPagesTables extends Migration
{
    public function up()
    {
        Schema::create('contact_pages', function (Blueprint $table) {
            createDefaultTableFields($table);
        });

        Schema::create('contact_page_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'contact_page');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('contact_page_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'contact_page');
        });

        Schema::create('contact_page_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'contact_page');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_page_revisions');
        Schema::dropIfExists('contact_page_translations');
        Schema::dropIfExists('contact_page_slugs');
        Schema::dropIfExists('contact_pages');
    }
}
