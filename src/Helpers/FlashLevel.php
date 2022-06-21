<?php

namespace A17\Twill\Helpers;

use MyCLabs\Enum\Enum;

class FlashLevel extends Enum
{
    /**
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * @var class-string<\error>
     */
    const ERROR = 'error';

    /**
     * @var string
     */
    const WARNING = 'caution';

    /**
     * @var string
     */
    const INFO = 'help';
}
