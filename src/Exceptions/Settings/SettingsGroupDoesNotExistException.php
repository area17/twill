<?php

namespace A17\Twill\Exceptions\Settings;

use Exception;

class SettingsGroupDoesNotExistException extends Exception
{
    public function __construct(string $group)
    {
        parent::__construct("The settings group '$group' does not exist");
    }
}
