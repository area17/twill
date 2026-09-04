<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\User;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

class LoginTest extends TestCase
{
    public function testCanRedirectToLogin(): void
    {
        $this->httpRequestAssert('/twill');

        $this->assertSame('http://twill.test/twill/login', url()->full());

        $this->assertSee('Forgot password');
    }

    public function testCanLogin(): void
    {
        $this->login();

        $this->assertAuthenticated();

        $this->assertSee('Media Library');

        $this->assertSee('Settings');

        $this->assertSee('Logout');
    }

    public function testCanLoginWithDifferentEmailCase(): void
    {
        // Force a case-sensitive collation so this test doesn't pass by
        // accident on a DB whose default collation already happens to be
        // case-insensitive (e.g. MySQL's utf8mb4_0900_ai_ci).
        $usersTable = (new User())->getTable();
        $passwordResetsTable = config('twill.password_resets_table', 'twill_password_resets');

        $originalCollation = DB::selectOne(
            'SELECT COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$usersTable, 'email']
        )->COLLATION_NAME;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("ALTER TABLE {$usersTable} MODIFY email VARCHAR(255) COLLATE utf8mb4_bin");
        DB::statement("ALTER TABLE {$passwordResetsTable} MODIFY email VARCHAR(255) COLLATE utf8mb4_bin");
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        try {
            $this->loginAs(
                strtoupper($this->superAdmin()->email),
                $this->superAdmin()->unencrypted_password
            );

            $this->assertAuthenticated();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement("ALTER TABLE {$usersTable} MODIFY email VARCHAR(255) COLLATE {$originalCollation}");
            DB::statement("ALTER TABLE {$passwordResetsTable} MODIFY email VARCHAR(255) COLLATE {$originalCollation}");
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function testCannotLoginWhenUserDisabled(): void
    {
        unset($this->superAdmin->unencrypted_password);
        $this->superAdmin->published = false;
        $this->superAdmin->save();

        $this->login();
        $this->assertGuest();
    }

    public function testCanLogout(): void
    {
        $this->login();

        $this->httpRequestAssert('/twill/logout', 'POST');

        $this->assertSee('Forgot password');
    }

    public function testGoogle2FA(): void
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
