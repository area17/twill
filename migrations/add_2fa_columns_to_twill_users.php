<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2faColumnsToTwillUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('twill.users_table', 'twill_users'), function (Blueprint $table) {
            $table->string('google_2fa_secret')->nullable();
            $table->boolean('google_2fa_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('twill.users_table', 'twill_users'), function (Blueprint $table) {
            $table->dropColumn('google_2fa_secret');
            $table->dropColumn('google_2fa_enabled');
        });
    }
}
