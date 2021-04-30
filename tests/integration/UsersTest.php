<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;

class UsersTest extends TestCase
{
    protected function impersonateUser()
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

    public function createUser($name = null, $email = null)
    {
        $name = $name ?? $this->faker->name;
        $email = $email ?? $this->faker->email;

        $payload = [
            'name' => $name,
            'email' => $email,
            'role' => 'PUBLISHER',
            'languages' => [
                [
                    'shortlabel' => 'EN',
                    'label' => 'English',
                    'value' => 'en',
                    'disabled' => false,
                    'published' => true,
                ],
            ],
        ];

        $this->ajax('/twill/users', 'POST', $payload)->assertStatus(200);

        return User::where('email', $email)->first();
    }

    public function testCanListUsers()
    {
        $this->ajax(
            '/twill/users?sortKey=email&sortDir=asc&page=1&offset=20&columns[]=bulk&columns[]=published&columns[]=name&columns[]=email&columns[]=role_value&filter=%7B%22status%22:%22published%22%7D'
        )->assertStatus(200);

        $this->assertJson($this->content());
    }

    public function testCanUpdateUser()
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

    public function testCanCreateUser()
    {
        $user = $this->createUser(
            $name = $this->faker->name,
            $email = $this->faker->email
        );

        $this->assertEquals($name, $user->name);
    }

    public function testCanEditUser()
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

    public function testCanImpersonateUser()
    {
        $this->impersonateUser();
    }

    public function testCanStopImpersonatingUser()
    {
        $this->impersonateUser();

        $this->httpRequestAssert('/twill/users/impersonate/stop');

        $this->assertNull(session()->get('impersonate'));
    }
}
