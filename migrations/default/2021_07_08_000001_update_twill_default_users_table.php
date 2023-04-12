<?php

use A17\Twill\Models\User;
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
                $table->dateTime('last_login_at')->nullable();
                $table->dateTime('registered_at')->nullable();
                $table->boolean('require_new_password')->default(false);
            });

            $this->seedNewFields();
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
                $table->dropColumn('last_login_at');
                $table->dropColumn('registered_at');
                $table->dropColumn('require_new_password');
            });
        }
    }

    private function seedNewFields()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                if (!empty($user->password)) {
                    $user->registered_at = $user->created_at;
                }

                $user->save();
            }
        });
    }
}
