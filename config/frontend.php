<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Toolkit Frontend assets configuration
    |--------------------------------------------------------------------------
    |
    | This allows you to setup frontend helpers related settings.
    |
    |
     */
    'rev_manifest_path' => public_path('dist/rev-manifest.json'),
    'dev_assets_path' => '/dev',
    'dist_assets_path' => '/dist',
    'svg_sprites_path' => 'sprites.svg', // relative to dev/dist assets paths
    'svg_sprites_use_hash_only' => true,
    'views_path' => 'front',
    'home_route_name' => 'home',
];
