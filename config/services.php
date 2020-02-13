<?php

return [
    'cloudfront' => [
        'key' => env('CLOUDFRONT_KEY', env('AWS_KEY')),
        'secret' => env('CLOUDFRONT_SECRET', env('AWS_SECRET')),
        'distribution' => env('CLOUDFRONT_DISTRIBUTION', env('AWS_CLOUDFRONT_DISTRIBUTION')),
        'sdk_version' => env('CLOUDFRONT_SDK_VERSION', env('AWS_SDK_VERSION', '2017-10-30')),
        'region' => env('CLOUDFRONT_REGION', env('AWS_REGION', 'us-east-1')),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_CALLBACK_URL', '/login/oauth/callback/github'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL', '/login/oauth/callback/google'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_CALLBACK_URL', '/login/oauth/callback/facebook'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_CALLBACK_URL', '/login/oauth/callback/twitter'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_CALLBACK_URL', '/login/oauth/callback/linkedin'),
    ],

    'gitlab' => [
        'client_id' => env('GITLAB_CLIENT_ID'),
        'client_secret' => env('GITLAB_CLIENT_SECRET'),
        'redirect' => env('GITLAB_CALLBACK_URL', '/login/oauth/callback/gitlab'),
    ],

    'bitbucket' => [
        'client_id' => env('BITBUCKET_CLIENT_ID'),
        'client_secret' => env('BITBUCKET_CLIENT_SECRET'),
        'redirect' => env('BITBUCKET_CALLBACK_URL', '/login/oauth/callback/bitbucket'),
    ],

];
