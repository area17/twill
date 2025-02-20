<?php

namespace A17\Twill\Helpers;

use MyCLabs\Enum\Enum;

class FlashLevel extends Enum
{
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'caution';
    public const INFO = 'help';
}
