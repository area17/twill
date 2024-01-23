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
        $twillUsersTable = config('twill.users_table', 'twill_users');

        if (Schema::hasTable($twillUsersTable) && !Schema::hasColumn($twillUsersTable, 'language')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->string('language')->nullable();
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

        if (Schema::hasTable($twillUsersTable) && Schema::hasColumn($twillUsersTable, 'language')) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->dropColumn('language');
            });
        }
    }
};
