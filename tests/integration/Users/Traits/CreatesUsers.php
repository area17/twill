<?php

namespace A17\Twill\Tests\Integration\Users\Traits;

use A17\Twill\Models\User;

trait CreatesUsers
{
    public function createUser($name = null, $email = null): User
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
}
