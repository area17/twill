<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use A17\Twill\Models\Permission;
use Illuminate\Support\Facades\Hash;

class PermissionsTest extends PermissionsTestBase
{
    protected function getPackageProviders($app)
    {
        // This config must be set before loading TwillServiceProvider to select
        // between AuthServiceProvider and PermissionAuthServiceProvider
        $app['config']->set('twill.enabled.permissions-management', true);
        $app['config']->set('twill.enabled.settings', true);

        return parent::getPackageProviders($app);
    }

    public function createUser($role)
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'published' => true,
        ]);
        $user->password = Hash::make($user->email);
        $user->role_id = $role->id;
        $user->save();

        return $user;
    }

    public function createRole($roleName)
    {
        $role = Role::create([
            'name' => $roleName,
            'published' => true,
            'in_everyone_group' => true,
        ]);

        return $role;
    }

    public function attachRolePermission(&$role, $permissionName)
    {
        $permission = Permission::whereName($permissionName)->first();
        $role->permissions()->attach($permission->id);
    }

    public function testRolePermissions()
    {
        $role = $this->createRole('Tester');
        $user = $this->createUser($role);

        // User is logged in
        $this->loginUser($user);
        $this->httpRequestAssert('/twill');
        $this->assertSee($user->name);

        // User can access settings if permitted
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 403);
        $this->attachRolePermission($role, 'edit-settings');
        $this->httpRequestAssert('/twill/settings/seo', 'GET', [], 200);
    }
}
