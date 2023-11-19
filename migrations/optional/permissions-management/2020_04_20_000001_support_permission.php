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
        $permissionsTableName = config('twill.permissions_table', 'permissions');
        $rolesTableName = config('twill.roles_table', 'roles');

        if (!Schema::hasTable($permissionsTableName)
            && !Schema::hasTable('groups')
            && !Schema::hasTable($rolesTableName)
            && !Schema::hasTable('permission_twill_user')
            && !Schema::hasTable('group_twill_user')
            && !Schema::hasTable('group_permission')
            && !Schema::hasTable('permission_role')
        ) {
            Schema::create($permissionsTableName, function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name');
                $table->string('display_name')->nullable();
                $table->bigInteger('permissionable_id')->unsigned()->nullable();
                $table->string('permissionable_type')->nullable();
                $table->boolean('is_default')->default(false);
            });

            Schema::create('groups', function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_everyone_group')->default(false);
            });

            Schema::create($rolesTableName, function (Blueprint $table) {
                createDefaultTableFields($table);
                $table->string('name', 255)->nullable();
                $table->boolean('in_everyone_group')->default(true);
                $table->integer('position')->unsigned()->nullable();
            });

            Schema::create('permission_twill_user', function (Blueprint $table) use($permissionsTableName) {
                $table->bigInteger('twill_user_id')->unsigned()->nullable();
                $table->foreign('twill_user_id')
                    ->references('id')
                    ->on(config('twill.users_table', 'twill_users'))
                    ->onDelete('cascade');

                $table->bigInteger('permission_id')->unsigned()->nullable();
                $table->foreign('permission_id')
                    ->references('id')
                    ->on($permissionsTableName)
                    ->onDelete('cascade');
            });

            Schema::create('group_twill_user', function (Blueprint $table) {
                $table->bigInteger('twill_user_id')->unsigned()->nullable();
                $table->foreign('twill_user_id')
                    ->references('id')
                    ->on(config('twill.users_table', 'twill_users'))
                    ->onDelete('cascade');

                $table->bigInteger('group_id')->unsigned()->nullable();
                $table->foreign('group_id')
                    ->references('id')
                    ->on('groups')
                    ->onDelete('cascade');

                $table->integer('position')->unsigned()->nullable();
            });

            Schema::create('group_permission', function (Blueprint $table) use($permissionsTableName) {
                $table->bigInteger('permission_id')->unsigned()->nullable();
                $table->foreign('permission_id')
                    ->references('id')
                    ->on($permissionsTableName)
                    ->onDelete('cascade');

                $table->bigInteger('group_id')->unsigned()->nullable();
                $table->foreign('group_id')
                    ->references('id')
                    ->on('groups')
                    ->onDelete('cascade');
            });

            Schema::create('permission_role', function (Blueprint $table) use($permissionsTableName, $rolesTableName) {
                $table->bigInteger('permission_id')->unsigned()->nullable();
                $table->foreign('permission_id')
                    ->references('id')
                    ->on($permissionsTableName)
                    ->onDelete('cascade');

                $table->bigInteger('role_id')->unsigned()->nullable();
                $table->foreign('role_id')
                    ->references('id')
                    ->on($rolesTableName)
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
        $permissionsTableName = config('twill.permissions_table', 'permissions');
        $rolesTableName = config('twill.roles_table', 'roles');


        Schema::dropIfExists('permission_twill_user');
        Schema::dropIfExists('group_twill_user');
        Schema::dropIfExists('group_permission');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists($permissionsTableName);
        Schema::dropIfExists('groups');
        Schema::dropIfExists($rolesTableName);
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

        $position = 1;

        foreach ($roles as $role_name => $role_permissions) {
            $role = Role::create([
                'name' => $role_name,
                'published' => true,
                'in_everyone_group' => $role_name === 'Guest' ? false : true,
                'position' => $position++,
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
