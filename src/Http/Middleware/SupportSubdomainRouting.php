<?php

namespace A17\Twill\Http\Middleware;

use A17\Twill\Exceptions\NoNavigationForSubdomainException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class SupportSubdomainRouting
{
    public static array $routingOriginal = [];

    public function handle(Request $request, Closure $next)
    {
        $parameter = 'subdomain';
        $subdomain = $request->route()->parameter($parameter) ?? key(config('twill.app_names'));

        if (config('twill.active_subdomain') !== $subdomain) {
            config(['twill.active_subdomain' => $subdomain]);
        }

        if (self::$routingOriginal === []) {
            // We store the original routing here so that every request we can start from the base config set.
            self::$routingOriginal = [
                'app.name' => config('twill.app_names'),
                'twill-navigation' => config('twill-navigation'),
                'twill.dashboard.modules' => config('twill.dashboard.modules'),
            ];
        }

        // Set subdomain as default URL parameter to not have
        // to add it manually when using route helpers
        URL::defaults([$parameter => $subdomain]);

        $blockLayout = View::exists($subdomain . '.layouts.block') ? ($subdomain . '.layouts.block') : 'site.layouts.blocks';

        if (! isset(self::$routingOriginal['twill-navigation'][$subdomain])) {
            throw new NoNavigationForSubdomainException('Subdomain: ' . $subdomain);
        }

        config([
            'app.name' => self::$routingOriginal['app.name'][$subdomain] ?? config('app.name'),
            'twill-navigation' => self::$routingOriginal['twill-navigation'][$subdomain],
            'twill.dashboard.modules' => self::$routingOriginal['twill.dashboard.modules'][$subdomain] ?? config('twill.dashboard.modules'),
            'twill.block_editor.block_single_layout' => $blockLayout,
        ]);

        $request->route()->forgetParameter($parameter);

        return $next($request);
    }
}
