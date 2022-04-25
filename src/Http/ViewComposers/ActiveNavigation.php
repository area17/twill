<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ActiveNavigation
{
    public function __construct(protected Request $request)
    {
    }

    /**
     * Binds data to the view.
     */
    public function compose(View $view): void
    {
        if ($this->request->route()) {
            $routeName = $this->request->route()->getName();

            $activeMenus = explode('.', $routeName);

            //starts at 1 because first segment of all back route name is 'admin'
            $view_active_variables['_global_active_navigation'] = $activeMenus[1];

            if (count($activeMenus) > 2) {
                $view_active_variables['_primary_active_navigation'] = $activeMenus[2];
            } elseif (count($this->request->route()->parameters()) > 0) {
                $view_active_variables['_primary_active_navigation'] = Arr::first($this->request->route()->parameters());
            }

            if (count($activeMenus) > 3) {
                $view_active_variables['_secondary_active_navigation'] = $activeMenus[3] !== 'index' ? $activeMenus[3] : $activeMenus[2];
            }

            $with = array_merge($view_active_variables, Arr::only($view->getData(), [
                '_global_active_navigation',
                '_primary_active_navigation',
                '_secondary_active_navigation',
            ]));

            $view->with($with);
        }
    }
}
