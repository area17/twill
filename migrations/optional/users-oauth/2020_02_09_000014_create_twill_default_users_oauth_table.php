<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultUsersOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('twill.enabled.users-oauth', false)) {
            $twillUsersTable = config('twill.users_table', 'twill_users');
            $twillOauthTable = config('twill.users_oauth_table', 'twill_users_oauth');

            if (!Schema::hasTable($twillOauthTable)) {
                Schema::create($twillOauthTable, function (Blueprint $table) use ($twillUsersTable) {
                    $table->bigIncrements('id');
                    $table->timestamps();
                    $table->string('token')->index();
                    $table->string('provider')->index();
                    $table->string('avatar')->nullable();
                    $table->string('oauth_id')->index();
                    $table->bigInteger('user_id')->unsigned()->index();
                    $table->foreign('user_id', 'fk_twill_users_id')->references('id')->on($twillUsersTable)->onDelete('cascade');
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
        Schema::dropIfExists(config('twill.users_oauth_table', 'twill_users_oauth'));
    }
}
