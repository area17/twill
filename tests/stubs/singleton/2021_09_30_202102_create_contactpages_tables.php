<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactpagesTables extends Migration
{
    public function up()
    {
        Schema::create('contactpages', function (Blueprint $table) {
            createDefaultTableFields($table);
        });

        Schema::create('contactpage_translations', function (Blueprint $table) {
            createDefaultTranslationsTableFields($table, 'contactpage');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('contactpage_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'contactpage');
        });

        Schema::create('contactpage_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'contactpage');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactpage_revisions');
        Schema::dropIfExists('contactpage_translations');
        Schema::dropIfExists('contactpage_slugs');
        Schema::dropIfExists('contactpages');
    }
}
