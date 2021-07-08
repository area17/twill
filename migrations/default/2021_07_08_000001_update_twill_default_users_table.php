<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTwillDefaultUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillUsersTable = config('twill.users_table', 'twill_users');

        if (Schema::hasTable($twillUsersTable)) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->unsignedInteger('role_id')->nullable();
                $table->boolean('is_superadmin')->default(false);
                $table->boolean('activated')->default(false);
                $table->dateTime('last_login_at')->nullable();
                $table->dateTime('registered_at')->nullable();
                $table->boolean('require_new_password')->default(false);
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

        if (Schema::hasTable($twillUsersTable)) {
            Schema::table($twillUsersTable, function (Blueprint $table) {
                $table->dropColumn('role_id');
                $table->dropColumn('is_superadmin');
                $table->dropColumn('activated');
                $table->dropColumn('last_login_at');
                $table->dropColumn('registered_at');
                $table->dropColumn('require_new_password');
            });
        }
    }
}
