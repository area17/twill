<?php

namespace A17\Twill;

use A17\Twill\Exceptions\Settings\SettingsGroupDoesNotExistException;
use A17\Twill\Exceptions\Settings\SettingsSectionDoesNotExistException;
use A17\Twill\Helpers\BlockRenderer;
use A17\Twill\Models\Block;
use A17\Twill\Services\Blocks\Block as BlockService;
use A17\Twill\Services\Settings\SettingsGroup;

class TwillAppSettings
{
    /**
     * @var SettingsGroup[]
     */
    private array $settingsGroups = [];

    private array $groupBlockCache = [];

    public function registerSettingsGroup(SettingsGroup $section): void
    {
        $this->settingsGroups[$section->getName()] = $section;
    }

    public function registerSettingsGroups(SettingsGroup ...$sections): void
    {
        foreach ($sections as $section) {
            $this->registerSettingsGroup($section);
        }
    }

    /**
     * @return array<string, SettingsGroup>
     */
    public function getAllGroups(): array
    {
        return $this->settingsGroups;
    }

    /**
     * @return array<int, SettingsGroup>
     */
    public function getGroupsForNavigation(): array
    {
        return array_filter(
            $this->settingsGroups,
            fn(SettingsGroup $group) => ! $group->shouldNotAutoRegisterInMenu() && $group->isAvailable()
        );
    }

    public function settingsAreEnabled(): bool
    {
        return config('twill.enabled.settings', false);
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

        if ($block->getRelated($key)->isNotEmpty()) {
            return $block->getRelated($key);
        }

        if ($block->children->where('child_key', $key)->isNotEmpty()) {
            return $block->children->where('child_key', $key);
        }

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
        $group = $this->settingsGroups[$groupName] ?? null;

        if (! $group) {
            throw new SettingsGroupDoesNotExistException($groupName);
        }

        return $group;
    }

    public function getGroupDataForSectionAndName(string $group, string $section): Block
    {
        $cacheKey = $group . $section;
        if (array_key_exists($cacheKey, $this->groupBlockCache)) {
            return $this->groupBlockCache[$cacheKey];
        }

        $groupObject = $this->getGroupForGroupAndSectionName($group, $section);

        return $this->groupBlockCache[$cacheKey] = $groupObject->getSettingsModel()->blocks()
                                                               ->where('editor_name', $section)
                                                               ->where('parent_id', null)
                                                               ->firstOrFail();
    }

    public function getBlockServiceForGroupAndSection(string $group, string $section): BlockService
    {
        $groupObject = $this->getGroupForGroupAndSectionName($group, $section)->getSettingsModel();

        $groupObject->registerSettingBlocks();

        $block = $this->getGroupDataForSectionAndName($group, $section);

        return BlockRenderer::getNestedBlocksForBlock($block, $groupObject, '');
    }

    /**
     * This checks and fetches the correct group. It throws exceptions when the data is missing.
     */
    private function getGroupForGroupAndSectionName(string $group, string $section): SettingsGroup
    {
        $groupObject = $this->getGroupForName($group);

        if (! $groupObject->hasSection($section)) {
            throw new SettingsSectionDoesNotExistException($groupObject, $section);
        }

        return $groupObject;
    }
}
