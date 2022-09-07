<?php

namespace A17\Twill;

use A17\Twill\Exceptions\Settings\SettingsGroupDoesNotExistException;
use A17\Twill\Exceptions\Settings\SettingsSectionDoesNotExistException;
use A17\Twill\Models\Block;
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

    public function getTranslated(string $identifier): mixed
    {
        [$group, $section, $key] = $this->getGroupSectionAndKeyFromIdentifier($identifier);

        $block = $this->getGroupDataForSectionAndName($group, $section);

        return $block->translatedInput($key);
    }

    public function get(string $identifier): mixed
    {
        [$group, $section, $key] = $this->getGroupSectionAndKeyFromIdentifier($identifier);

        $block = $this->getGroupDataForSectionAndName($group, $section);

        return $block->input($key);
    }

    private function getGroupSectionAndKeyFromIdentifier(string $identifier): array
    {
        $sections = explode('.', $identifier);

        if (count($sections) !== 3) {
            // At some point we can improve on this.
            throw new \Exception('Currently only 3 levels are supported for getting settings.');
        }

        return $sections;
    }

    public function getGroupForName(string $groupName): SettingsGroup
    {
        $group = self::$settingsGroups[$groupName] ?? null;

        if (!$group) {
            throw new SettingsGroupDoesNotExistException($group);
        }

        return $group;
    }

    public function getGroupDataForSectionAndName(string $group, string $section): Block
    {
        $groupObject = $this->getGroupForGroupAndSectionName($group, $section);
        return $groupObject->getSettingsModel()->blocks()->where('editor_name', $section)->firstOrFail();
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
