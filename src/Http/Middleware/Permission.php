<?php

namespace A17\Twill\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, Closure $next)
    {
        if (config('twill.enabled.permission') && !Auth::user()->is_superadmin) {
            if(config('twill.support_subdomain_admin_routing') && $activeSubdomain = config('twill.active_subdomain')) {
                foreach(Auth::user()->groups as $group) {
                    if (in_array($activeSubdomain, $group->subdomains_access)) {
                        return $next($request);
                    }
                };
                abort(403);
            }
        }
        return $next($request);
    }
}
