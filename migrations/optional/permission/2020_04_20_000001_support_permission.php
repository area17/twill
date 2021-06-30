<?php

use A17\Twill\Models\Role;
use A17\Twill\Models\Group;
use A17\Twill\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupportPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('permissions')
            && !Schema::hasTable('groups')
            && !Schema::hasTable('roles')
            && !Schema::hasTable('permission_twill_user')
            && !Schema::hasTable('group_twill_user')
            && !Schema::hasTable('group_permission')
            && !Schema::hasTable('permission_role')
        ) {
            Schema::create('permissions', function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name');
                $table->string('display_name')->nullable();
                $table->unsignedBigInteger('permissionable_id')->nullable();
                $table->string('permissionable_type')->nullable();
                $table->boolean('is_default')->default(false);
            });

            Schema::create('groups', function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_everyone_group')->default(false);
            });

            Schema::create('roles', function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name', 255)->nullable();
                $table->boolean('in_everyone_group')->default(true);
            });

            Schema::create('permission_twill_user', function (Blueprint $table) {
                $table->foreignId('twill_user_id')
                    ->nullable()
                    ->constrained(config('twill.users_table', 'twill_users'))
                    ->onDelete('cascade');

                $table->foreignId('permission_id')
                    ->nullable()
                    ->constrained('permissions')
                    ->onDelete('cascade');
            });

            Schema::create('group_twill_user', function (Blueprint $table) {
                $table->foreignId('twill_user_id')
                    ->nullable()
                    ->constrained(config('twill.users_table', 'twill_users'))
                    ->onDelete('cascade');

                $table->foreignId('group_id')
                    ->nullable()
                    ->constrained('groups')
                    ->onDelete('cascade');

                $table->integer('position')->unsigned()->nullable();
            });

            Schema::create('group_permission', function (Blueprint $table) {
                $table->foreignId('permission_id')
                    ->nullable()
                    ->constrained('permissions')
                    ->onDelete('cascade');

                $table->foreignId('group_id')
                    ->nullable()
                    ->constrained('groups')
                    ->onDelete('cascade');
            });

            Schema::create('permission_role', function (Blueprint $table) {
                $table->foreignId('permission_id')
                    ->nullable()
                    ->constrained('permissions')
                    ->onDelete('cascade');

                $table->foreignId('role_id')
                    ->nullable()
                    ->constrained('roles')
                    ->onDelete('cascade');
            });

            $this->seedBasicPermissions();
            $this->seedDefaultRoles();
            $this->seedDefaultGroup();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_twill_user');
        Schema::dropIfExists('group_twill_user');
        Schema::dropIfExists('group_permission');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('roles');
    }

    private function seedBasicPermissions()
    {
        // Seed default global permissions
        $global_permissions = Permission::available(Permission::SCOPE_GLOBAL);

        foreach ($global_permissions as $permission) {
            $displayName = implode(' ', array_map(function ($word) {
                return ucfirst($word);
            }, explode('-', $permission)));
            Permission::create([
                'name' => $permission,
                'display_name' => $displayName,
                'published' => true,
                'is_default' => true,
            ]);
        }
    }

    private function seedDefaultRoles()
    {
        // Default roles and their permissions
        $roles = [
            'Owner' => Permission::available(Permission::SCOPE_GLOBAL),
            'Administrator' => array_diff(Permission::available(Permission::SCOPE_GLOBAL), ["edit-user-roles", "manage-modules"]),
            'Team' => [],
            'Guest' => [],
        ];

        foreach ($roles as $role_name => $role_permissions) {
            $role = Role::create([
                'name' => $role_name,
                'published' => true,
                'in_everyone_group' => $role_name === 'Guest' ? false : true,
            ]);
            $role->permissions()->attach(Permission::whereIn("name", $role_permissions)->pluck('id'));
        }
    }

    private function seedDefaultGroup()
    {
        $everyoneGroup = Group::create(
            [
                'name' => 'Everyone',
                'description' => 'The default everyone group',
                'published' => true
            ]
        );

        $everyoneGroup->is_everyone_group = true;
        $everyoneGroup->save();
    }
}
