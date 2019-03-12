<?php

namespace Sb4yd3e\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const VIEWONLY = 'View only';
    const PUBLISHER = 'Publisher';
    const ADMIN = 'Admin';
}
