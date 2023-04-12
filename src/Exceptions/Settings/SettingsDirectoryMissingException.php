<?php

namespace A17\Twill\Exceptions\Settings;

use Exception;

class SettingsDirectoryMissingException extends Exception
{
    public function __construct(string $directory)
    {
        parent::__construct($directory . ' directory is expected to exist but could not be found.');
    }
}
