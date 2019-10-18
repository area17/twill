<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use PragmaRX\Google2FA\Google2FA;

class LoginTest extends TestCase
{
    public function testCanRedirectToLogin()
    {
        $crawler = $this->followingRedirects()->call('GET', '/twill');

        $this->assertSame('http://twill.test/twill/login', url()->full());

        $this->assertStringContainsString(
            'Forgot password',
            $crawler->getContent()
        );

        $crawler->assertStatus(200);
    }

    public function testCanLogin()
    {
        $crawler = $this->login();

        $this->assertStringContainsString(
            'Media Library',
            $crawler->getContent()
        );

        $this->assertStringContainsString('Settings', $crawler->getContent());

        $this->assertStringContainsString('Logout', $crawler->getContent());
    }

    public function testCanLogout()
    {
        $this->login();

        $crawler = $this->followingRedirects()->call('GET', '/twill/logout');

        $this->assertStringContainsString(
            'Forgot password',
            $crawler->getContent()
        );
    }

    public function testGoogle2FA()
    {
        $user = User::where('email', $this->superAdmin()->email)->first();

        $user->generate2faSecretKey();

        $user->update(['google_2fa_enabled' => true]);

        $crawler = $this->login();

        $this->assertStringContainsString(
            'One-time password',
            $crawler->getContent()
        );

        $crawler = $this->followingRedirects()->call(
            'POST',
            '/twill/login-2fa',
            [
                'verify-code' => 'INVALID CODE',
            ]
        );

        $this->assertStringContainsString(
            'Your one time password is invalid.',
            $crawler->getContent()
        );

        $crawler = $this->followingRedirects()->call(
            'POST',
            '/twill/login-2fa',
            [
                'verify-code' => (new Google2FA())->getCurrentOtp(
                    $user->google_2fa_secret
                ),
            ]
        );

        $crawler->assertStatus(200);

        $this->assertStringContainsString(
            'Media Library',
            $crawler->getContent()
        );
    }
}
