<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorAuthColumnsToTwillUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillUsersTable = config('twill.users_table', 'twill_users');

        if (Schema::hasTable($twillUsersTable) && !Schema::hasColumn($twillUsersTable, 'google_2fa_secret')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->string('google_2fa_secret')->nullable();
            });
        }

        if (Schema::hasTable($twillUsersTable) && !Schema::hasColumn($twillUsersTable, 'google_2fa_enabled')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->boolean('google_2fa_enabled')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $twillUsersTable = config('twill.users_table', 'twill_users');

        if (Schema::hasTable($twillUsersTable) && Schema::hasColumn($twillUsersTable, 'google_2fa_secret')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->dropColumn('google_2fa_secret');
            });
        }

        if (Schema::hasTable($twillUsersTable) && Schema::hasColumn($twillUsersTable, 'google_2fa_enabled')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->dropColumn('google_2fa_enabled');
            });
        }
    }
}
