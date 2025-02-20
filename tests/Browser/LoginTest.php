<?php

namespace A17\Twill\Tests\Browser;

use Laravel\Dusk\Browser;

class LoginTest extends BrowserTestCase
{
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/twill');

            $browser->type('email', $this->superAdmin->email);
            $browser->type('password', 'admin');
            $browser->press('Login');
            $browser->waitForText('You don\'t have any activity yet.');
            $browser->assertSee('You don\'t have any activity yet.');
        });
    }
}
