<?php

namespace A17\Twill;

use A17\Twill\Exceptions\Settings\SettingsGroupDoesNotExistException;
use A17\Twill\Exceptions\Settings\SettingsSectionDoesNotExistException;
use A17\Twill\Services\Settings\SettingsGroup;

class TwillAppSettings
{
    /**
     * @var SettingsGroup[]
     */
    public static array $settingsGroups = [];

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
            fn(SettingsGroup $group) => !$group->shouldNotAutoRegisterInMenu()
        );
    }

    public function settingsAreEnabled(): bool
    {
        return config('twill.enabled.settings');
    }

    /**
     * The set function will override the full section's settings.
     *
     * The group is the one defined in your TwillAppSettings::registerSettingsGroup call.
     * The section is the name of the file in your settings folder.
     */
    public function set(string $group, string $section): void
    {
        $group = $this->getGroupForGroupAndSectionName($group, $section);
    }

    public function getGroupForName(string $groupName): SettingsGroup
    {
        $group = self::$settingsGroups[$groupName] ?? null;

        if (!$group) {
            throw new SettingsGroupDoesNotExistException($group);
        }

        return $group;
    }

    /**
     * This checks and fetches the correct group. It throws exceptions when the data is missing.
     */
    private function getGroupForGroupAndSectionName(string $group, string $section): SettingsGroup
    {
        $groupObject = $this->getGroupForName($group);

        if (!$groupObject->hasSection($section)) {
            throw new SettingsSectionDoesNotExistException($groupObject, $section);
        }

        return $groupObject;
    }
}
