<?php

namespace A17\Twill\Tests\Integration;

abstract class PermissionsTestBase extends TestCase
{
    public function loginUser($user)
    {
        $this->loginAs($user->email, $user->email);
    }
}
