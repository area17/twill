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
}
