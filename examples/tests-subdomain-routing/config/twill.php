<?php

return [
    'support_subdomain_admin_routing' => true,
    'admin_app_subdomain' => 'admin',
    'active_subdomain' => null,
    'app_names' => [
        'subdomain1' => 'App 1 name',
        'subdomain2' => 'App 2 name',
    ],

    /**
     * If this is not present, the middleware src/Http/Middleware/SupportSubdomainRouting.php,
     * throws an exception.
     *
     * TypeError: key(): Argument #1 ($array) must be of type array, null given in /var/www/html/vendor/area17/twill/src/Http/Middleware/SupportSubdomainRouting.php:30
     */
    'dashboard' => [
        'modules' => [
            'subdomain1' => [],
            'subdomain2' => [],
        ]
    ]
];
