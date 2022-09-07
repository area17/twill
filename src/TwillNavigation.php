<?php

namespace A17\Twill;

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

    public function addLink(NavigationLink $link): void
    {
        if (config('twill-navigation', []) !== []) {
            throw new \Exception('You cannot combine twill-navigation and TwillNavigation');
        }

        $this->links[] = $link;
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
     * @return array<int, NavigationLink>
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
            }

            $link->setChildren($children);

            $links[] = $link;
        }

        return $links;
    }

    private function transformLegacyToNew(string $key, array $legacy): NavigationLink
    {
        if ($legacy['route'] ?? false) {
            $link = NavigationLink::make()->forRoute($legacy['route']);
        } elseif ($legacy['singleton'] ?? false) {
            $link = NavigationLink::make()->forSingleton($key);
        } else {
            $link = NavigationLink::make()->forModule($key);
        }

        if ($link && ($legacy['title'] ?? false)) {
            $link->title($legacy['title']);
        }

        return $link;
    }

    private function getSettingsTree(): ?NavigationLink
    {
        $settingsNavigationGroups = TwillAppSettings::getGroupsForNavigation();
        if ($settingsNavigationGroups !== []) {
            $links = [];
            foreach ($settingsNavigationGroups as $group) {
                $links[] = NavigationLink::make()
                    ->title($group->getLabel())
                    ->forRoute($group->getRoute(), ['group' => $group->getName()]);
            }

            return NavigationLink::make()
                ->title('Settings')
                ->forRoute('twill.app.settings')
                ->setChildren($links);
        }

        return null;
    }

    /**
     * A work in progress to simplify the navigation tree.
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
            ->withAttributes(['data-medialib-btn'])
            ->title(twillTrans('twill::lang.nav.media-library'))
            ->onlyWhen(function () {
                return Auth::user()->can('access-media-library');
            });
        $tree['right'][] = NavigationLink::make()
            ->title(twillTrans('twill::lang.nav.open-live-site'))
            ->onlyWhen(fn() => config('twill.enable.site-link', false))
            ->toExternalUrl(config('app.url'));

        return $tree;
    }
}
