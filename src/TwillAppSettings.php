<?php

namespace A17\Twill;

use A17\Twill\Services\Settings\SettingsGroup;

class TwillAppSettings
{
    public static array $settingsGroups = [];

    /**
     * @var string
     *   The name of the folder: resources/twill/settings/{$name}
     *   This folder will be parsed for the setting sections.
     *   This should not contain spaces or special characters.
     * @var string
     *   The label to use for this settings page.
     */
    public function registerSettingsGroup(SettingsGroup $section): void
    {
        self::$settingsGroups[$section->getName()] = $section;
    }

    /**
     * @return array<string, SettingsGroup>
     */
    public function getAllGroups(): array
    {
        return self::$settingsGroups;
    }

    /**
     * @return array<int, SettingsGroup>
     */
    public function getGroupsForNavigation(): array
    {
        return array_filter(
            self::$settingsGroups,
            fn (SettingsGroup $group) => ! $group->shouldNotAutoRegisterInMenu()
        );
    }
}
