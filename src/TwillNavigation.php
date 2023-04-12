<?php

namespace A17\Twill;

use A17\Twill\Exceptions\Navigation\CannotCombineNavigationBuilderWithLegacyConfig;
use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\View\Components\Navigation\NavigationLink;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TwillNavigation
{
    /**
     * @var array<int, NavigationLink>
     */
    private array $links = [];

    /**
     * @var array<int, NavigationLink>
     */
    private array $secondaryRequestLinks = [];

    public function addLink(NavigationLink $link): self
    {
        if (config('twill-navigation', []) !== []) {
            throw new CannotCombineNavigationBuilderWithLegacyConfig(
                'You cannot combine twill-navigation and TwillNavigation'
            );
        }

        $this->links[] = $link;

        return $this;
    }

    public function addSecondaryNavigationForCurrentRequest(NavigationLink $link): void
    {
        $this->secondaryRequestLinks[] = $link;
    }

    public function getSecondaryRequestLinks(): array
    {
        return $this->secondaryRequestLinks;
    }

    public function getActiveSecondaryNavigationLink(): ?NavigationLink
    {
        foreach ($this->getActivePrimaryNavigationLink()?->getChildren() ?? [] as $entry) {
            if ($entry->isActive()) {
                return $entry;
            }
        }

        return null;
    }

    public function getActivePrimaryNavigationLink(): ?NavigationLink
    {
        foreach ($this->buildNavigationTree() as $section) {
            $activeLink = Arr::first(
                array_filter($section, function (NavigationLink $entry) {
                    return $entry->isActive() ?? false;
                })
            );

            if ($activeLink !== null) {
                return $activeLink;
            }
        }

        return null;
    }

    /**
     * @return NavigationLink[]
     */
    private function getLeftNavigation(): array
    {
        if ($this->links !== []) {
            return $this->links;
        }

        $links = [];

        // Convert the original twill-navigation.
        foreach (config('twill-navigation', []) as $key => $primaryItem) {
            $link = $this->transformLegacyToNew($key, $primaryItem);

            $children = [];
            foreach ($primaryItem['primary_navigation'] ?? [] as $childKey => $nestedItem) {
                $children[] = $secondary = $this->transformLegacyToNew($childKey, $nestedItem);

                $secondaryChildren = [];
                foreach ($nestedItem['secondary_navigation'] ?? [] as $tertiaryKey => $tertiaryItem) {
                    $secondaryChildren[] = $this->transformLegacyToNew($tertiaryKey, $tertiaryItem);
                }

                $secondary->setChildren($secondaryChildren);
                $secondary->doNotAddSelfAsFirstChild();
            }

            $link->setChildren($children);
            $link->doNotAddSelfAsFirstChild();

            $links[] = $link;
        }

        return $links;
    }

    private function transformLegacyToNew(string $key, array $legacy): NavigationLink
    {
        if ($legacy['route'] ?? false) {
            $link = NavigationLink::make()->forRoute($legacy['route'], $legacy['params'] ?? []);
        } elseif ($legacy['singleton'] ?? false) {
            $link = NavigationLink::make()->forSingleton($key);
        } else {
            $link = NavigationLink::make()->forModule($key);
        }

        if ($legacy['title'] ?? false) {
            $link->title($legacy['title']);
        }

        if ($legacy['can'] ?? false) {
            $link->onlyWhen(fn () => (bool) Auth::user()?->can($legacy['can']));
        }

        return $link;
    }

    private function getSettingsTree(): ?NavigationLink
    {
        $settingsNavigationGroups = TwillAppSettings::getGroupsForNavigation();
        if ($settingsNavigationGroups !== []) {
            $mainSettingsGroup = NavigationLink::make()
                ->title(twillTrans('twill::lang.nav.settings'));

            $links = [];

            foreach ($settingsNavigationGroups as $group) {
                if ($links === []) {
                    // Set the first item to the main settings link.
                    $mainSettingsGroup->forRoute($group->getRoute(), ['group' => $group->getName()])
                        ->doNotAddSelfAsFirstChild(true);
                }
                $links[] = NavigationLink::make()
                    ->title($group->getLabel())
                    ->forRoute($group->getRoute(), ['group' => $group->getName()]);
            }

            if ($links !== []) {
                return $mainSettingsGroup->setChildren($links);
            }
        }

        return null;
    }

    /**
     * @return array<string, array<int, NavigationLink>>
     */
    public function buildNavigationTree(): array
    {
        $tree = [];

        $tree['left'] = $this->getLeftNavigation();
        $tree['right'] = [];

        if ($settings = $this->getSettingsTree()) {
            $tree['right'][] = $settings;
        }

        $tree['right'][] = NavigationLink::make()
            ->withAttributes(['data-medialib-btn', 'data-closenav-btn'])
            ->title(twillTrans('twill::lang.nav.media-library'))
            ->onlyWhen(fn() => config('twill.enabled.media-library') && (Auth::user()?->can('access-media-library') ?? false));
        $tree['right'][] = NavigationLink::make()
            ->title(twillTrans('twill::lang.nav.open-live-site'))
            ->onlyWhen(fn() => config('twill.enabled.site-link', false))
            ->toExternalUrl(config('app.url'));

        // Filter the final tree before handing it off.
        foreach ($tree as $groupKey => $group) {
            $tree[$groupKey] = Arr::where($group, function (NavigationLink $link): bool {
                return $link->shouldShow();
            });
        }

        return $tree;
    }

    public function clear(): void
    {
        $this->links = [];
    }
}
