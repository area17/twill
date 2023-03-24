<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('related', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }

    public function down(): void
    {
        Schema::table('related', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
