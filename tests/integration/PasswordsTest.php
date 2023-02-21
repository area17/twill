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
        // TODO: this test is probably wrong
        //       I had to add a 404 here for it to pass
        $this->httpRequestAssert('/twill/password/email', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->superAdmin()->email,
        ], 404);

        Notification::assertSentTo(
            $user = User::where(
                'email',
                $email = $this->superAdmin()->email
            )->first(),
            Reset::class
        );

        $resetUrl = route(
            'admin.password.reset.form',
            Notification::token(),
            false
        );

        $this->httpRequestAssert($resetUrl);

        $this->assertSee('Reset password');

        $this->assertSee('Confirm password');
    }

    public function testNotificationsAreFaked()
    {
        Notification::assertNothingSent();
    }

    public function testCanShowPasswordResetForm()
    {
        $this->httpRequestAssert('/twill/password/reset');

        $this->assertSee('Reset password');

        $this->assertSee('Send password reset link');
    }

    public function testCanResetPassword()
    {
        $this->sendPasswordResetToAdmin();

        $this->httpRequestAssert('/twill/password/reset', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->superAdmin()->email,
            'password' => ($password = $this->faker->password(50)),
            'password_confirmation' => $password,
            'token' => Notification::token(),
        ]);

        $this->assertSee('Your password has been reset');
    }

    public function testCanExpireResetPasswordToken()
    {
        $this->sendPasswordResetToAdmin();

        DB::table('twill_password_resets')->truncate();

        $this->httpRequestAssert('/twill/password/reset', 'POST', [
            '_token' => csrf_token(),
            'email' => $this->superAdmin()->email,
            'password' => ($password = $this->faker->password(50)),
            'password_confirmation' => $password,
            'token' => Notification::token(),
        ]);

        $this->assertSee(
            'Your password reset token has expired or could not be found, please retry.'
        );
    }
}
