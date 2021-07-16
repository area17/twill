<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionsTest extends PermissionsTestBase
{
    public function configTwill($app)
    {
        parent::configTwill($app);

        $app['config']->set('twill.enabled.permissions-management', true);
        $app['config']->set('twill.enabled.settings', true);
    }

    public function createUser($role)
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
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

    public function testRolePermissions()
    {
        $role = $this->createRole('Tester');
        $user = $this->createUser($role);

        // User is logged in
        $this->loginUser($user);
        $this->httpRequestAssert('/twill');
        $this->assertSee($user->name);

    }
}
