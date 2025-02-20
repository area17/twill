<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Services - Twill Oauth login configuration
    |--------------------------------------------------------------------------
    |
    | This array allows you to enable/disable Oauth providers
    |
    | Possible values (from socialite): facebook, twitter, linkedin, google, github, gitlab and bitbucket
    | See https://laravel.com/docs/6.x/socialite
    |
     */
    'providers' => ['google'],

    /*
    |--------------------------------------------------------------------------
    | This boolean controls whether a user should be created or not
    | when a new user is logging in through Oauth
    |--------------------------------------------------------------------------
    |
    | Possible values: true, false
    |
     */
    'create_user_with_default_role' => true,

    /*
    |--------------------------------------------------------------------------
    | New user default role name (legacy)
    |--------------------------------------------------------------------------
    |
    | Possible values: VIEWONLY, PUBLISHER, ADMIN
    |
     */
    'default_role' => 'VIEWONLY',

    /*
    |--------------------------------------------------------------------------
    | New user default role name (permissions-manager)
    |--------------------------------------------------------------------------
    |
    | Defaults roles: Owner, Administrator, Team, Guest
    |
     */
    'permissions_default_role' => 'Guest',

];
