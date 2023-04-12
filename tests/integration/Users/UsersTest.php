<?php

namespace A17\Twill\Tests\Integration\Users;

use A17\Twill\Models\User;
use A17\Twill\Tests\Integration\TestCase;
use A17\Twill\Tests\Integration\Users\Traits\CreatesUsers;

class UsersTest extends TestCase
{
    use CreatesUsers;

    public function testDummy(): void
    {
        $this->assertTrue(true);
    }

    protected function impersonateUser(): User
    {
        $this->httpRequestAssert('/twill');

        $this->assertSee('Admin');

        $user = $this->createUser();

        $this->httpRequestAssert("/twill/users/impersonate/{$user->id}");

        $this->assertSee(e($user->name));

        $this->assertEquals($user->id, session()->get('impersonate'));

        return $user;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListUsers(): void
    {
        $this->ajax(
            '/twill/users?sortKey=email&sortDir=asc&page=1&offset=20&columns[]=bulk&columns[]=published&columns[]=name&columns[]=email&columns[]=role_value&filter=%7B%22status%22:%22published%22%7D'
        )->assertStatus(200);

        $this->assertJson($this->content());
    }

    public function testCanUpdateUser(): void
    {
        $user = $this->createUser(
            $name = $this->faker->name,
            $email = $this->faker->email
        );

        $payload = [
            'email' => $email,
            'name' => ($newName = $this->faker->name),
            'cmsSaveType' => 'update',
        ];

        $crawler = $this->ajax("/twill/users/{$user->id}", 'PUT', $payload)->assertStatus(200);

        $crawler->assertStatus(200);

        $this->assertEquals($name, $user->name);

        $user->refresh();

        $this->assertEquals($newName, $user->name);
    }

    public function testCanCreateUser(): void
    {
        $user = $this->createUser(
            $name = $this->faker->name,
            $email = $this->faker->email
        );

        $this->assertEquals($name, $user->name);
    }

    public function testCanEditUser(): void
    {
        $user = User::where(
            'email',
            $email = $this->superAdmin()->email
        )->first();

        $user->google_2fa_enabled = true;

        $user->save();

        $this->httpRequestAssert("/twill/users/{$user->id}/edit");

        $this->assertSee($email);
    }

    public function testCanImpersonateUser(): void
    {
        $this->impersonateUser();
    }

    public function testCanStopImpersonatingUser(): void
    {
        $this->impersonateUser();

        $this->httpRequestAssert('/twill/users/impersonate/stop');

        $this->assertNull(session()->get('impersonate'));
    }
}
