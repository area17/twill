<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
     * Note: Rolling back this migration may truncate tokens longer than 255 characters.
     * Ensure no tokens exceed this length before rolling back.
     *
     * @return void
     */
    public function down()
    {
        if (config('twill.enabled.users-oauth', false)) {
            $twillOauthTable = config('twill.users_oauth_table', 'twill_users_oauth');

            if (Schema::hasTable($twillOauthTable) && Schema::hasColumn($twillOauthTable, 'token')) {
                // Check if any tokens exceed 255 characters before rolling back
                $longTokensCount = DB::table($twillOauthTable)
                    ->whereRaw('LENGTH(token) > 255')
                    ->count();

                if ($longTokensCount > 0) {
                    throw new \RuntimeException(
                        "Cannot rollback migration: {$longTokensCount} token(s) exceed 255 characters. " .
                        "Please truncate or remove these tokens before rolling back this migration."
                    );
                }

                Schema::table($twillOauthTable, function (Blueprint $table) {
                    $table->string('token')->change();
                });
            }
        }
    }
};
