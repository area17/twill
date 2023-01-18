<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $twillPasswordResetsTable = config('twill.password_resets_table', 'twill_password_resets');
        $twillUsersTable = config('twill.users_table', 'twill_users');

        Schema::table($twillPasswordResetsTable, function (Blueprint $table) use ($twillUsersTable) {
            $table->foreign('email')
                ->references('email')->on($twillUsersTable)
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        $twillPasswordResetsTable = config('twill.password_resets_table', 'twill_password_resets');
        Schema::table($twillPasswordResetsTable, function (Blueprint $table) {
            $table->dropForeign('email');
        });
    }
};
