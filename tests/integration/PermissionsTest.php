<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function configTwill($app)
    {
        parent::configTwill($app);

        $app['config']->set('twill.enabled.permissions-management', true);
    }

    public function createUser($roleName="Guest")
    {
        $user = User::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ]);
        $user->password = Hash::make($user->email);
        $user->role_id = Role::whereName($roleName)->first()->id;
        $user->save();

        return $user;
    }

    public function loginUser($user)
    {
        $this->loginAs($user->email, $user->email);
    }

    public function testDoesNotCrash()
    {
        $this->loginUser($guest = $this->createUser());

        $this->httpRequestAssert('/twill');

        $this->assertSee($guest->name);
    }
}
