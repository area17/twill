<?php

namespace A17\Twill\View\Components\Layout;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class GlobalNavigation extends Component
{
    protected ?string $activeMenu = null;

    private function activeMenu(): string
    {
        if ($this->activeMenu === null) {
            $routeName = request()->route()->getName();

            $activeMenus = explode('.', $routeName);

            $this->activeMenu = $activeMenus[1];
        }

        return $this->activeMenu;
    }

    public function getNavigationItems(): array
    {
        $final = [];
        foreach (config('twill-navigation', []) as $global_navigation_key => $global_navigation_element) {
            $is_settings = $global_navigation_key === 'settings';
            $is_module = $is_settings || ($global_navigation_element['module'] ?? false);
            $gate = $is_settings ? 'edit-settings' : ($global_navigation_element['can'] ?? 'access-module-list');

            if (!$is_module || Auth::user()->can($gate, $global_navigation_key)) {
                $final[] = [
                    'is_active' => isActiveNavigation(
                        $global_navigation_element,
                        $global_navigation_key,
                        $this->activeMenu()
                    ),
                    'href' => getNavigationUrl($global_navigation_element, $global_navigation_key),
                    'target_blank' => isset($global_navigation_element['target']) && $global_navigation_element['target'] === 'external',
                    'title' => $global_navigation_element['title'],
                ];
            }
        }

        return $final;
    }

    public function render(): string
    {
        return view('twill::partials.navigation._global_navigation', ['nav_items' => $this->getNavigationItems()]);
    }
}
