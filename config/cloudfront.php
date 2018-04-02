<?php

return [
    'cloudfront' => [
        'key' => env('CLOUDFRONT_KEY', env('AWS_KEY')),
        'secret' => env('CLOUDFRONT_SECRET', env('AWS_SECRET')),
        'distribution' => env('CLOUDFRONT_DISTRIBUTION', env('AWS_CLOUDFRONT_DISTRIBUTION')),
        'sdk_version' => env('CLOUDFRONT_SDK_VERSION', env('AWS_SDK_VERSION', '2017-10-30')),
        'region' => env('CLOUDFRONT_REGION', env('AWS_REGION', 'us-east-1')),
    ],
];
