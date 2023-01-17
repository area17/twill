<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = DB::connection()->getDatabaseName();
        $twillPasswordResetsTable = config('twill.password_resets_table', 'twill_password_resets');
        $twillUsersTable = config('twill.users_table', 'twill_users');

        try {
            $outcome = DB::select(DB::raw(<<<SQL
SELECT TABLE_NAME ,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = '$schema' AND 
    REFERENCED_TABLE_NAME = '$twillUsersTable' AND
    TABLE_NAME = '$twillPasswordResetsTable'
SQL
            ));
        } catch (Exception $e) {
            // Do nothing as we are on an unsupported db.
            return;
        }

        if ($outcome[0] && $outcome[0]->COLUMN_NAME === 'email') {
            // This is not supported in sqlite, for that reason we also include it in the original migration but we still
            // can check if it already is here.
            return;
        }

        // At this point, the foreign key does not exist yet so we can add it.
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
