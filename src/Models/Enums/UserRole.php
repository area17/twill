<?php

namespace A17\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const OWNER = 'Owner';
    const ADMIN = 'Admin';
    const TEAM = 'Team';
    const GUEST = 'Guest';
}
