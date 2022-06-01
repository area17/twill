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
        foreach (config('twill-navigation') as $navigationKey => $navigationElement) {
            $isSetting = $navigationKey === 'settings';
            $isModule = $isSetting || ($navigationElement['module'] ?? false);
            $gate = $isSetting ? 'edit-settings' : ($navigationElement['can'] ?? 'access-module-list');

            if (!$isModule || Auth::user()->can($gate, $navigationKey)) {
                $final[] = [
                    'is_active' => isActiveNavigation(
                        $navigationElement,
                        $navigationKey,
                        $this->activeMenu()
                    ),
                    'href' => getNavigationUrl($navigationElement, $navigationKey),
                    'target_blank' => isset($navigationElement['target']) && $navigationElement['target'] === 'external',
                    'title' => $navigationElement['title'],
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
