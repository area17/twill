<?php

return [
    'libraries' => [
        'driver' => 's3',
        'key' => env('S3_KEY', env('AWS_KEY', env('AWS_ACCESS_KEY_ID'))),
        'secret' => env('S3_SECRET', env('AWS_SECRET', env('AWS_SECRET_ACCESS_KEY'))),
        'region' => env('S3_REGION', env('AWS_REGION', env('AWS_DEFAULT_REGION', 'us-east-1'))),
        'bucket' => env('S3_BUCKET', env('AWS_BUCKET')),
        'root' => env('S3_ROOT', env('AWS_ROOT')),
        'use_https' => env('S3_UPLOADER_USE_HTTPS', env('S3_USE_HTTPS', env('AWS_USE_HTTPS', true))),
    ],
];
