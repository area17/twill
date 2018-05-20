<?php

namespace A17\Twill\Helpers;

use MyCLabs\Enum\Enum;

class FlashLevel extends Enum
{
    const SUCCESS = 'success';
    const ERROR = 'error';
    const WARNING = 'caution';
    const INFO = 'help';
}
