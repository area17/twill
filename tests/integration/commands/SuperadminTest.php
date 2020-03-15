<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Models\User;
use A17\Twill\Tests\Integration\TestCase;

class SuperadminTest extends TestCase
{
    public function testCanExecuteSuperadminCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:superadmin')
                ->expectsQuestion(
                    'Enter an email',
                    $this->superAdmin(true)->email
                )
                ->expectsQuestion(
                    'Enter a password',
                    $this->superAdmin()->password
                )
                ->expectsQuestion(
                    'Confirm the password',
                    $this->superAdmin()->password
                )
                ->run()
        );

        $this->assertNotNull(
            User::where('email', $this->superAdmin()->email)->first()
        );
    }
}
