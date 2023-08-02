<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Facades\TwillPermissions;
use MyCLabs\Enum\Enum;

class SwappableRolesTest extends PermissionsTestBase
{
    public function testDefaultRoles(): void
    {
        $this->assertEquals(
            ['VIEWONLY' => 'View only', 'PUBLISHER' => 'Publisher', 'ADMIN' => 'Admin'],
            TwillPermissions::roles()::toArray()
        );
    }

    public function testCustomRoles(): void {
        $rolesEnum = new class('Custom only') extends Enum {
            public const CUSTOMONLY = 'Custom only';
        };

        TwillPermissions::setRoleEnum($rolesEnum::class);
        
        $this->assertEquals(
            ['CUSTOMONLY' => 'Custom only'],
            TwillPermissions::roles()::toArray()
        );
    }
}
