<?php

$localRootPrefix = storage_path('app/public/');

$mediaLocalConfig = [
    'driver' => 'local',
    'visibility' => 'public',
    'root' => $localRootPrefix . trim(config('twill.media_library.local_path'), '/ '),
];

$fileLocalConfig = [
    'driver' => 'local',
    'visibility' => 'public',
    'root' => $localRootPrefix . trim(config('twill.file_library.local_path'), '/ '),
];

$s3Config = [
    'driver' => 's3',
    'key' => env('S3_KEY', env('AWS_KEY', env('AWS_ACCESS_KEY_ID'))),
    'secret' => env('S3_SECRET', env('AWS_SECRET', env('AWS_SECRET_ACCESS_KEY'))),
    'region' => env('S3_REGION', env('AWS_REGION', env('AWS_DEFAULT_REGION', 'us-east-1'))),
    'bucket' => env('S3_BUCKET', env('AWS_BUCKET')),
    'root' => env('S3_ROOT', env('AWS_ROOT', '')),
    'url' => env('S3_URL', env('AWS_URL')),
    'endpoint' => env('S3_ENDPOINT', env('AWS_ENDPOINT')),
    'use_path_style_endpoint' => env('S3_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', false)),
];

$azureConfig = [
    'driver' => 'azure',
    'key' => env('AZURE_ACCOUNT_KEY'),
    'name' => env('AZURE_ACCOUNT_NAME'),
    'container' => env('AZURE_CONTAINER', 'public'),
    'endpoint-suffix' => env('AZURE_ENDPOINT_SUFFIX', 'core.windows.net'),
    'use_https' => env('AZURE_UPLOADER_USE_HTTPS', env('AZURE_USE_HTTPS', true)),
];

$mediaConfigByEndpointType = [
    'local' => $mediaLocalConfig,
    's3' => $s3Config,
    'azure' => $azureConfig,
];

$fileConfigByEndpointType = [
    'local' => $fileLocalConfig,
    's3' => $s3Config,
    'azure' => $azureConfig,
];

return [
    'twill_media_library' => $mediaConfigByEndpointType[config('twill.media_library.endpoint_type')],
    'twill_file_library' => $fileConfigByEndpointType[config('twill.file_library.endpoint_type')],
];
