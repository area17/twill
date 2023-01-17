<?php

namespace A17\Twill\Tests\Integration\Users;

use A17\Twill\Notifications\Reset;
use A17\Twill\Tests\Integration\TestCase;
use A17\Twill\Tests\Integration\Users\Traits\CreatesUsers;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class UserEmailTest extends TestCase
{
    use CreatesUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testPasswordResetEmail(): void
    {
        $user = $this->createUser('rob@example.org', 'rob@example.org');

        Mail::fake();
        Notification::fake();

        app()->make(PasswordBrokerManager::class)
            ->broker('twill_users')
            ->sendResetLink(['email' => 'rob@example.org']);

        Notification::assertSentTo($user, Reset::class);

        $user->forceDelete();

        $user = $this->createUser('rob@example.org', 'rob@example.org');

        app()->make(PasswordBrokerManager::class)
            ->broker('twill_users')
            ->sendResetLink(['email' => 'rob@example.org']);

        Notification::assertSentTo($user, Reset::class);
    }
}
