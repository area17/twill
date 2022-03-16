<?php

namespace A17\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    public const VIEWONLY = 'View only';

    public const PUBLISHER = 'Publisher';

    public const ADMIN = 'Admin';
}
