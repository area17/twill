<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubdomainsAccessColumnToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('groups') && !Schema::hasColumn('groups', 'subdomains_access')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->json('subdomains_access')->nullable();
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
        if (Schema::hasTable('groups') && Schema::hasColumn('groups', 'subdomains_access')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->removeColumn('subdomains_access');
            });
        }
    }
}
