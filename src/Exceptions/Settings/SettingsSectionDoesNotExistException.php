<?php

namespace A17\Twill\Exceptions\Settings;

use A17\Twill\Services\Settings\SettingsGroup;
use Exception;

class SettingsSectionDoesNotExistException extends Exception
{
    public function __construct(SettingsGroup $group, string $section)
    {
        $groupName = $group->getName();
        parent::__construct("The settings group '$groupName' does not have a '$section' section");
    }
}
