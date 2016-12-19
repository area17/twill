<?php

return [
    'driver' => 's3',
    'key' => env('S3_KEY'),
    'secret' => env('S3_SECRET'),
    'region' => env('S3_REGION', 'us-east-1'),
    'bucket' => env('S3_BUCKET'),
];
