<?php

return [
    'libraries' => [
        'driver' => 's3',
        'key' => env('S3_KEY', env('AWS_KEY')),
        'secret' => env('S3_SECRET', env('AWS_SECRET')),
        'region' => env('S3_REGION', env('AWS_REGION', 'us-east-1')),
        'bucket' => env('S3_BUCKET', env('AWS_BUCKET')),
        'use_https' => env('S3_USE_HTTPS', env('AWS_USE_HTTPS', true)),
    ],
];
