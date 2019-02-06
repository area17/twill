<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
        });

        Scheme::create('users_groups', function (Blueprint $table) {
           $table->unsignedInteger('twill_user_id');
           $table->foreign('user_id')
                 ->references('id')->on('twill_users')
                 ->onDelete('cascade');

           $table->unsignedInteger('groups_id');
           $table->foreign('group_id')
                 ->references('id')->on('groups')
                 ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('users_groups');
    }
}
