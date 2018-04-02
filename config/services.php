<?php

return [

    'cloudfront' => [
        'key' => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
        'distribution' => env('CLOUDFRONT_DISTRIBUTION'),
        'sdk_version' => env('COULDFRONT_SDK_VERSION'),
        'region' => env('COULDFRONT_REGION'),
    ],

];
