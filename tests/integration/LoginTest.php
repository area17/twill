<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use PragmaRX\Google2FA\Google2FA;

class LoginTest extends TestCase
{
    public function testCanRedirectToLogin()
    {
        $this->request('/twill')->assertStatus(200);

        $this->assertSame('http://twill.test/twill/login', url()->full());

        $this->assertStringContainsString('Forgot password', $this->content());
    }

    public function testCanLogin()
    {
        $this->login();

        $this->assertStringContainsString('Media Library', $this->content());

        $this->assertStringContainsString('Settings', $this->content());

        $this->assertStringContainsString('Logout', $this->content());
    }

    public function testCanLogout()
    {
        $this->login();

        $this->request('/twill/logout');

        $this->assertStringContainsString('Forgot password', $this->content());
    }

    public function testGoogle2FA()
    {
        $user = User::where('email', $this->superAdmin()->email)->first();

        $user->generate2faSecretKey();

        $user->update(['google_2fa_enabled' => true]);

        $this->login();

        $this->assertStringContainsString(
            'One-time password',
            $this->content()
        );

        $this->request('/twill/login-2fa', 'POST', [
            'verify-code' => 'INVALID CODE',
        ]);

        $this->assertStringContainsString(
            'Your one time password is invalid.',
            $this->content()
        );

        $this->request('/twill/login-2fa', 'POST', [
            'verify-code' => (new Google2FA())->getCurrentOtp(
                $user->google_2fa_secret
            ),
        ])->assertStatus(200);

        $this->assertStringContainsString('Media Library', $this->content());
    }
}
