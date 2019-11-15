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
    | New user default role
    |--------------------------------------------------------------------------
    |
    | Possible values: VIEWONLY, PUBLISHER, ADMIN
    |
     */
    'default_role' => 'VIEWONLY'

];
