<?php

namespace A17\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const VIEWONLY = 'View only';
    const PUBLISHER = 'Publisher';
    const ADMIN = 'Admin';
}
