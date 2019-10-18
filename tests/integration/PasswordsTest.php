<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use A17\Twill\Notifications\Reset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PasswordsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        Notification::macro('token', function () {
            return $this
                ->notifications['A17\Twill\Models\User'][1]['A17\Twill\Notifications\Reset'][0]['notification']->token;
        });
    }

    protected function sendPasswordResetToAdmin()
    {
        $this->request('/twill/password/email', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->getSuperAdmin()->email,
        ]);

        Notification::assertSentTo(
            $user = User::where(
                'email',
                $email = $this->getSuperAdmin()->email
            )->first(),
            Reset::class
        );

        $resetUrl = route(
            'admin.password.reset.form',
            Notification::token(),
            false
        );

        $crawler = $this->request($resetUrl);

        $this->assertStringContainsString(
            'Reset password',
            $crawler->getContent()
        );

        $this->assertStringContainsString(
            'Confirm password',
            $crawler->getContent()
        );
    }

    public function testNotificationsAreFaked()
    {
        Notification::assertNothingSent();
    }

    public function testCanShowPasswordResetForm()
    {
        $crawler = $this->request('/twill/password/reset');

        $crawler->assertStatus(200);

        $this->assertStringContainsString(
            'Reset password',
            $crawler->getContent()
        );

        $this->assertStringContainsString(
            'Send password reset link',
            $crawler->getContent()
        );
    }

    public function testCanResetPassword()
    {
        $this->sendPasswordResetToAdmin();

        $crawler = $this->request('/twill/password/reset', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->getSuperAdmin()->email,
            'password' => ($password = $this->faker->password(50)),
            'password_confirmation' => $password,
            'token' => Notification::token(),
        ]);

        $this->assertStringContainsString(
            'Your password has been reset!',
            $crawler->getContent()
        );
    }

    public function testCanExpireResetPasswordToken()
    {
        $this->sendPasswordResetToAdmin();

        DB::table('twill_password_resets')->truncate();

        $crawler = $this->request('/twill/password/reset', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->getSuperAdmin()->email,
            'password' => ($password = $this->faker->password(50)),
            'password_confirmation' => $password,
            'token' => Notification::token(),
        ]);

        $this->assertStringContainsString(
            'Your password reset token has expired or could not be found, please retry.',
            $crawler->getContent()
        );
    }
}
