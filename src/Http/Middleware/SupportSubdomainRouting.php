<?php

namespace A17\Twill\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use URL;
use View;

class SupportSubdomainRouting
{
    public function handle(Request $request, Closure $next)
    {
        $parameter = 'subdomain';
        $subdomain = $request->route()->parameter($parameter) ?? key(config('twill.app_names'));

        // Set subdomain as default URL parameter to not have
        // to add it manually when using route helpers
        URL::defaults([$parameter => $subdomain]);

        $blockLayout = View::exists($subdomain . '.layouts.block') ? ($subdomain . '.layouts.block') : 'site.layouts.blocks';

        config([
            'app.name' => config('twill.app_names')[$subdomain] ?? config('app.name'),
            'twill-navigation' => config('twill-navigation')[$subdomain] ?? key(config('twill-navigation')),
            'twill.dashboard.modules' => config('twill.dashboard.modules')[$subdomain] ?? key(config('twill.dashboard.modules')),
            'twill.block_editor.block_single_layout' => $blockLayout,
        ]);

        $request->route()->forgetParameter($parameter);

        return $next($request);
    }
}
