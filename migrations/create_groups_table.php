<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            createDefaultTableFields($table);
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('group_user', function (Blueprint $table) {
           $table->unsignedInteger('twill_user_id');
           $table->foreign('twill_user_id')
                 ->references('id')->on(config('twill.users_table', 'twill_users'))
                 ->onDelete('cascade');

           $table->unsignedInteger('group_id');
           $table->foreign('group_id')
                 ->references('id')->on('groups')
                 ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('groups');
    }
}
