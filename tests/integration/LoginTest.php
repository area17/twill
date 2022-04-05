<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use PragmaRX\Google2FA\Google2FA;

class LoginTest extends TestCase
{
    public function testCanRedirectToLogin()
    {
        $this->httpRequestAssert('/twill');

        $this->assertSame('http://twill.test/twill/login', url()->full());

        $this->assertSee('Forgot password');
    }

    public function testCanLogin()
    {
        $this->login();

        $this->assertSee('Media Library');

        $this->assertSee('Settings');

        $this->assertSee('Logout');
    }

    public function testCanLogout()
    {
        $this->login();

        $this->httpRequestAssert('/twill/logout', 'POST');

        $this->assertSee('Forgot password');
    }

    public function testGoogle2FA()
    {
        $user = User::where('email', $this->superAdmin()->email)->first();

        $user->generate2faSecretKey();

        $user->update(['google_2fa_enabled' => true]);

        $this->login();

        $this->assertSee('One-time password');

        $this->httpRequestAssert('/twill/login-2fa', 'POST', [
            'verify-code' => 'INVALID CODE',
        ]);

        $this->assertSee('Your one time password is invalid.');

        $this->httpRequestAssert('/twill/login-2fa', 'POST', [
            'verify-code' => (new Google2FA())->getCurrentOtp(
                $user->google_2fa_secret
            ),
        ]);

        $this->assertSee('Media Library');
    }
}
