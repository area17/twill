<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ActiveNavigation
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Binds data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        if ($this->request->route()) {
            $routeName = $this->request->route()->getName();

            $activeMenus = explode('.', $routeName);

            //starts at 1 because first segment of all back route name is 'admin'
            $view_active_variables['_global_active_navigation'] = $activeMenus[1];

            if (count($activeMenus) > 2) {
                $view_active_variables['_primary_active_navigation'] = $activeMenus[2];
            } else if (count($this->request->route()->parameters()) > 0) {
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
