<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;

class SuperadminTest extends TestCase
{
    public function testCanExecuteSuperadminCommand()
    {
        $this->artisan('twill:superadmin')
            ->expectsQuestion('Enter an email', $this->superAdmin(true)->email)
            ->expectsQuestion('Enter a password', $this->superAdmin()->password)
            ->expectsQuestion(
                'Confirm the password',
                $this->superAdmin()->password
            );

        $this->assertNotNull(
            User::where('email', $this->superAdmin()->email)->first()
        );
    }
}
