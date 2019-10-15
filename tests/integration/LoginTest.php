<?php

namespace A17\Twill\Tests\Integration;

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
        $crawler = $this->followingRedirects()->call('POST', '/twill/login', [
            'email' => $this->getSuperAdmin()->email,
            'password' => $this->getSuperAdmin()->password,
        ]);

        $this->assertStringContainsString(
            'Media Library',
            $crawler->getContent()
        );

        $this->assertStringContainsString('Settings', $crawler->getContent());

        $this->assertStringContainsString('Logout', $crawler->getContent());
    }
}
