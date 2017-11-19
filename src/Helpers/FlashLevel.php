<?php

namespace A17\CmsToolkit\Helpers;

use MyCLabs\Enum\Enum;

class FlashLevel extends Enum
{
    const SUCCESS = 'success';
    const ERROR = 'error';
    const WARNING = 'caution';
    const INFO = 'help';
}
