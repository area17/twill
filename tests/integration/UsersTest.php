<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;

class UsersTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListUsers()
    {
        $crawler = $this->ajax(
            '/twill/users?sortKey=email&sortDir=asc&page=1&offset=20&columns[]=bulk&columns[]=published&columns[]=name&columns[]=email&columns[]=role_value&filter=%7B%22status%22:%22published%22%7D'
        );

        $this->assertJson($crawler->getContent());
    }

    public function testCanCreateUser()
    {
        $data = [
            'name' => ($name = $this->faker->name),
            'email' => ($email = $this->faker->email),
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

        $this->ajax('/twill/users', 'POST', $data);

        $user = User::where('email', $email)->first();

        $this->assertEquals($name, $user->name);
    }
}
