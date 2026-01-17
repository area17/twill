<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('twill.enabled.users-oauth', false)) {
            $twillOauthTable = config('twill.users_oauth_table', 'twill_users_oauth');

            if (Schema::hasTable($twillOauthTable) && Schema::hasColumn($twillOauthTable, 'token')) {
                Schema::table($twillOauthTable, function (Blueprint $table) {
                    $table->text('token')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('twill.enabled.users-oauth', false)) {
            $twillOauthTable = config('twill.users_oauth_table', 'twill_users_oauth');

            if (Schema::hasTable($twillOauthTable) && Schema::hasColumn($twillOauthTable, 'token')) {
                Schema::table($twillOauthTable, function (Blueprint $table) {
                    $table->string('token')->change();
                });
            }
        }
    }
};
