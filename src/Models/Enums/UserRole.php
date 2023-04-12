<?php

namespace A17\Twill\Models\Enums;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    /**
     * @var string
     */
    public const VIEWONLY = 'View only';

    /**
     * @var string
     */
    public const PUBLISHER = 'Publisher';

    /**
     * @var string
     */
    public const ADMIN = 'Admin';
}
